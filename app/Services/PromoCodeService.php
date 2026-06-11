<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\TicketType;
use Illuminate\Support\Str;
use RuntimeException;

class PromoCodeService
{
    public function applyToTicket(TicketType $ticket, int $attendeeCount, ?string $code, ?string $email = null, ?int $userId = null): array
    {
        $pricing = $ticket->getRegistrationPricing($attendeeCount);
        $pricing['subtotal_before_promo'] = $pricing['subtotal'];
        $pricing['promo_discount'] = 0.0;
        $pricing['promo_code'] = null;
        $pricing['promo_code_id'] = null;
        $pricing['ticket_discount_savings'] = round((float) ($pricing['base_subtotal'] ?? 0) - (float) ($pricing['subtotal'] ?? 0), 2);
        $pricing['total_savings'] = $pricing['savings'] ?? $pricing['ticket_discount_savings'];

        $normalizedCode = Str::upper(trim((string) $code));
        if ($normalizedCode === '') {
            return [
                'pricing' => $pricing,
                'promo_code' => null,
            ];
        }

        $promoCode = PromoCode::query()
            ->where('event_id', $ticket->event_id)
            ->whereRaw('UPPER(code) = ?', [$normalizedCode])
            ->first();

        if (!$promoCode) {
            throw new RuntimeException('Promo code not found.');
        }

        $this->assertPromoCodeIsEligible($promoCode, $ticket, $attendeeCount, $email, $userId);

        $subtotalBeforePromo = round((float) ($pricing['subtotal'] ?? 0), 2);
        $discountAmount = $this->calculateDiscountAmount($promoCode, $subtotalBeforePromo);
        $finalTotal = round(max($subtotalBeforePromo - $discountAmount, 0), 2);

        $pricing['promo_discount'] = $discountAmount;
        $pricing['promo_code'] = $promoCode->code;
        $pricing['promo_code_id'] = $promoCode->id;
        $pricing['total'] = $finalTotal;
        $pricing['ticket_discount_savings'] = round((float) ($pricing['base_subtotal'] ?? 0) - $subtotalBeforePromo, 2);
        $pricing['total_savings'] = round((float) $pricing['base_subtotal'] - $finalTotal, 2);
        $pricing['savings'] = round((float) $pricing['base_subtotal'] - $finalTotal, 2);
        $pricing['per_attendee_amounts'] = $this->applyDiscountAcrossAttendees(
            $pricing['per_attendee_amounts'] ?? [],
            $finalTotal
        );

        return [
            'pricing' => $pricing,
            'promo_code' => $promoCode,
        ];
    }

    public function assertPromoCodeIsEligible(PromoCode $promoCode, TicketType $ticket, int $attendeeCount, ?string $email = null, ?int $userId = null): void
    {
        if (!$promoCode->is_active) {
            throw new RuntimeException('This promo code is inactive.');
        }

        if (!$promoCode->isWithinWindow()) {
            throw new RuntimeException('This promo code is not currently valid.');
        }

        if ($promoCode->ticket_type_id && (int) $promoCode->ticket_type_id !== (int) $ticket->id) {
            throw new RuntimeException('This promo code is not valid for the selected ticket.');
        }

        if ($promoCode->min_attendee_count && $attendeeCount < (int) $promoCode->min_attendee_count) {
            throw new RuntimeException('This promo code requires at least ' . $promoCode->min_attendee_count . ' attendee(s).');
        }

        if ($promoCode->max_attendee_count && $attendeeCount > (int) $promoCode->max_attendee_count) {
            throw new RuntimeException('This promo code allows a maximum of ' . $promoCode->max_attendee_count . ' attendee(s).');
        }

        $completedRedemptions = $promoCode->redemptions()->where('status', 'completed');

        if ($promoCode->usage_limit_total && $completedRedemptions->count() >= (int) $promoCode->usage_limit_total) {
            throw new RuntimeException('This promo code has reached its total usage limit.');
        }

        if ($promoCode->usage_limit_per_user) {
            $query = $promoCode->redemptions()->where('status', 'completed');

            if ($userId) {
                $query->where('user_id', $userId);
            } elseif (filled($email)) {
                $query->where('email', Str::lower(trim((string) $email)));
            } else {
                throw new RuntimeException('Email is required to validate this promo code.');
            }

            if ($query->count() >= (int) $promoCode->usage_limit_per_user) {
                throw new RuntimeException('This promo code has reached its usage limit for this user.');
            }
        }
    }

    public function calculateDiscountAmount(PromoCode $promoCode, float $subtotalBeforePromo): float
    {
        if ($subtotalBeforePromo <= 0) {
            return 0.0;
        }

        $discount = $promoCode->discount_type === 'percentage'
            ? round($subtotalBeforePromo * ((float) $promoCode->discount_value / 100), 2)
            : round((float) $promoCode->discount_value, 2);

        return round(min($discount, $subtotalBeforePromo), 2);
    }

    protected function applyDiscountAcrossAttendees(array $amounts, float $finalTotal): array
    {
        if (empty($amounts)) {
            return [];
        }

        $originalTotal = round(array_sum($amounts), 2);
        if ($originalTotal <= 0) {
            return $amounts;
        }

        $discounted = [];
        $running = 0.0;
        $count = count($amounts);

        foreach ($amounts as $index => $amount) {
            if ($index === $count - 1) {
                $discounted[] = round($finalTotal - $running, 2);
                continue;
            }

            $share = round(($amount / $originalTotal) * $finalTotal, 2);
            $discounted[] = $share;
            $running += $share;
        }

        return $discounted;
    }
}
