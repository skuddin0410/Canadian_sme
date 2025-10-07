<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use App\Models\Page;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        // Default Organization Info
        DB::table('settings')->insert([
            [
                'key' => 'company_name',
                'value' => 'CanadianSME Small Business Summit 2025',
            ],
            [
                'key' => 'company_address',
                'value' => '2800 Skymark Avenue, Suite 203 Mississauga, ON. Canada. L4W 5A6',
            ],
            [
                'key' => 'support_email',
                'value' => 'info@canadiansme.ca',
            ],
            [
                'key' => 'tax_name',
                'value' => '',
            ],
            [
                'key' => 'tax_percentage',
                'value' => '',
            ],
            [
                'key' => 'company_number',
                'value' => '',
            ],
            [
                'key' => 'email_subject',
                'value' => "Lorem Ipsum is simply",
            ],
            [
                'key' => 'email_content',
                'value' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
        ]);


        // Default Privacy Policy Text
        $privacy_policy="<h1>Introduction</h1>
<p>Thank you for deciding to participate in the Small Business Summit 2025. As the official organizer, Cmarketing Inc. assures complete data security and information protection. Cutting-edge data security and data handling policies are followed by us so that you do not have to worry about anything.</p>
<p>Before proceeding to purchasing tickets for Small Business Summit 2025, we would strongly recommend you go through our privacy policy statements as listed below. In case you have any further queries, kindly send an email to info@canadiansme.ca.</p>

<h2>What Information Do We Collect?</h2>
<p>Cmarketing Inc. collects a set of information from you - the buyer - during the ticket-purchasing process. This information includes, but is not limited to:</p>
<ul>
    <li>Full name</li>
    <li>Email id</li>
    <li>Contact Number</li>
    <li>Company Name</li>
    <li>Province</li>
</ul>
<p>Please note that only users above the age of 16 can purchase tickets to Small Business Summit 2025. By purchasing a ticket, you automatically indicate that you are at least 16 years old and, as such, are capable of handling all responsibilities as a Small Business Summit 2025 attendee.</p>
<p>Apart from the above, we also collect ‘aggregate information - which refers to the insights obtained from buyer behaviour. This information includes things like your purchase decisions, interactions with notifications, duration of stay on a particular page or section, etc. - and are not ‘personal’ in any way. Cmarketing Inc. collects such information to gain actionable insights and improve its services as an event organizer on an ongoing basis.</p>

<h2>Why Do We Collect Information?</h2>
<p>Personally identifiable information and aggregate information are collected by Cmarketing Inc. for:</p>
<ul>
    <li>Ensuring smooth, secure service delivery (here, delivery of Small Business Summit 2025 ticket(s)).</li>
    <li>Creating and maintaining your user account on the Cmarketing Inc. platform.</li>
    <li>Sending personalized communications, including confirmation email(s) on the successful purchase of small Business Summit 2025 tickets.</li>
    <li>Sharing select promotional material, special offers, and other updates that we feel you might be interested in (you can opt-out of such emails).</li>
    <li>Implementing our terms of use.</li>
    <li>Enhancing the overall user-experience factor.</li>
</ul>
<p>In particular, the ‘personally identifiable information collected by Cmarketing Inc. is used for:</p>
<ul>
    <li>Processing ticket bookings (from bookings to ticket delivery).</li>
    <li>Communicating with you via emails (ticket purchase confirmations, event updates, cancellations, etc.).</li>
    <li>Performing sales analyses, tracking transactions, initiating marketing campaigns, and more.</li>
</ul>

<h2>GDPR Compliance</h2>
<p>Cmarketing Inc. is fully compliant with the latest GDPR regulations. For delivering seamless event ticketing services to you, we share your data with select authorized and licensed third-party service providers (partners). All partners of Cmarketing Inc., including Eventify (eventify.io), are GDPR-compliant.</p>

<h2>Use Of Cookies</h2>
<p>‘Cookies’ are small internet files that reside in your web browser. Cmarketing Inc. uses cookies to bring you a more personalized experience on the website. If you delete cookies from your browser, we will not be able to provide this extra level of personalization in our services. As such, accepting our cookie policy is strongly recommended.</p>

<h2>Data Protection Assurance By Cmarketing Inc.</h2>
<p>We are committed to keeping your personal, financial, and other related information safe. The information you provide is encrypted and stored in our secure database. Please note that without your consent, Cmarketing Inc. will never use/copy/sell/share your information with any unauthorized third party for advertising/marketing purposes.</p>
<p>We also request that you be vigilant about your data security while purchasing Small Business Summit 2025 tickets. Ensure that your account credentials are secure, use a trusted system to complete your transaction(s), and that your internet connection is not acting up. If you feel that your account has been compromised, let us know immediately at info@canadiansme.ca.</p>

<h2>Retention Of Data</h2>
<p>Cmarketing Inc. will retain your personal data for a time period that is deemed to be ‘reasonably necessary’. However, buyer information is generally not retained after a period of 3 years.</p>
<p>In select circumstances, we might retain buyer details to settle legal matters, litigations, disputes, and the like. The details are removed as soon as the concerned issue(s) are resolved.</p>

<h2>Photography & Videography Disclaimer</h2>
<p>By attending the CanadianSME Small Business Summit and/or the CanadianSME Small Business Summit, you acknowledge and agree to the following:</p>
<p>During the event, photographs and videos will be captured for promotional, marketing, and archival purposes.</p>
<p>These images and recordings may include attendees, exhibitors, speakers, and sponsors and may be taken in front of our media wall, within session rooms, on the exhibition floor, and other event areas.</p>
<p>By entering the event premises, you consent to being photographed, filmed, and/or otherwise recorded, and to the release, publication, exhibition, or reproduction of such media for use in:</p>
<ul>
    <li>News,</li>
    <li>Webcasts,</li>
    <li>Promotional materials,</li>
    <li>Social media,</li>
    <li>Advertising,</li>
    <li>and any other purpose by CanadianSME and its affiliates.</li>
</ul>
<p>You waive any right to inspect or approve the use of any photo or video that may be taken, and you understand that all media will remain the property of CanadianSME.</p>
<p>If you do not wish to be recorded or photographed, please inform the registration desk upon arrival and notify the media team on-site. We will do our best to accommodate your request, though we cannot guarantee complete exclusion from all event recordings or photos.</p>

<h2>What RIGHTS Do You Have?</h2>
<p>When you transact with Cmarketing Inc., you are protected by all relevant GDPR regulations. The rights you retain are as follows:</p>
<ul>
    <li>Right to information (You can, at any time, demand to know how your information is being used).</li>
    <li>Right to access (You can request a copy of all the personal records we possess).</li>
    <li>Right to rectification (You can request changes in personal records, particularly in case of inaccuracies).</li>
    <li>Right to erasure (You can instruct us to remove section(s) from your personal records).</li>
    <li>Right to processing objections (You can tell us to stop processing your personal data).</li>
</ul>

<h2>External Links</h2>
<p>The official organizer of the Small Business Summit 2025 is Cmarketing Inc. (www.smesummit.ca). Please note that there might be links to third-party entities on our website. Cmarketing Inc. does not, in any way, vouch for the authenticity, relevance or reliability of facts/opinions/stats present in such external resources.</p>
";
        DB::table('settings')->insert([
            [
                'key' => 'privacy_policy',
                'value' => $privacy_policy,
            ],
        ]);
        Page::where('slug','privacy')->update(['description' => $privacy_policy]);



        // Default Terms & Conditions Text
        $terms_conditions = "<h1>Thank you for your interest in participating in The Small Business Summit 2025.</h1>
<p></p>
<p>In this section, words like ‘holder’, ‘bearer’, and ‘purchaser’ will refer to you – the person who will buy the tickets to The Small Business Summit 2025. On the other hand, ‘our’, ‘us’, ‘we’, ‘issuer’, ‘provider’ and similar terms will indicate the organizers of The Small Business Summit 2025 – Cmarketing Inc.</p>
<p></p>
<p>By purchasing, allocating or possessing tickets to The Small Business Summit 2025, you automatically agree to our terms of use. If you have problems with any of the clauses listed below or do not agree with the risk allocations mentioned here, please do not purchase tickets or try to enter The Small Business Summit 2025 premises.</p>
<p></p>
<p>Please note that Cmarketing Inc. reserves the right to make changes, revisions, and amendments or add/remove sections from this set of terms and conditions – without prior notifications. Such changes will be duly reflected on the website of the organizer (www.smesummit.ca). These changes will relate back to the date of purchase of The Small Business Summit 2025 tickets.</p>
<p></p>
<p>The Small Business Summit 2025 ticket is FREE. The ticket you purchase represents a revocable license for you to access Metro Toronto Convention Centre on October when The Small Business Summit 2025 is scheduled to be hosted. The license includes the revocable right to attend The Small Business Summit 2025. This license can be revoked at any time by the issuer at its sole discretion, without any prior notifications or compensation options. You will not be granted entry to the venue if the license is revoked.</p>
<p></p>
<p>The bearer has to assume full responsibility for The Small Business Summit 2025 tickets once (s)he receives them. Cmarketing Inc. has no financial, legal, or otherwise liabilities in case the delivered tickets are stolen, lost, damaged, destroyed or rendered unusable in any other way.</p>
<p></p>
<p>Kindly note that www.smesummit.ca is the sole authorized source for purchasing The Small Business Summit 2025 tickets. If you obtain your ticket(s) from any other unauthorized source, you risk those tickets turning out to be counterfeit or reported to be stolen. The issuer has the right to dishonour such stolen/counterfeit tickets and declare them void.</p>
<p></p>
<p>The Small Business Summit 2025 tickets delivered to you are not meant to be used for illegal reselling, copying, reproducing, or misrepresenting in any form. Without our prior approval, you cannot use the ticket(s) for any promotional/advertising purposes either (including sweepstakes and competitions). Any proven record of such activities will result in immediate seizure and cancellation of your ticket(s).</p>
<p></p>
<p>Please note that The Small Business Summit 2025 tickets cannot be redeemed against cash, credits, or other benefits.</p>
<p></p>
<p>Cmarketing Inc. has the right to investigate if there has been any violation of The Small Business Summit 2025 ticketing terms and conditions. In case of any conflicts, the decision of Cmarketing Inc. will be deemed final (i.e., the issuer will remain the final arbiter in cases of disputes).</p>
<p></p>
<p>The event service will be deemed to have been delivered in its entirety as soon as The Small Business Summit 2025 starts on October. From that point on, no refund requests will be entertained.</p>
<p></p>
<p>If you are not able to access any particular section(s) of the venue – Metro Toronto Convention Centre – due to delays, federal regulations, organizer policies, weather-related problems, emergencies, venue shutdown/evacuation or any other reason, you will not be eligible for any refunds or future credits.</p>
<p></p>
<p>If The Small Business Summit 2025 is cancelled due to any factors or causes not in the control of the issuer, the latter will offer a partial or full refund, or postpone the event, or provide a comparable ‘make good’ option. These factors include, but are not limited to, natural disasters, federal/state announcements, strikes, delays in production, and the like. Once again, the decision of Cmarketing Inc. will be considered final.</p>
<p></p>
<p>If The Small Business Summit 2025 has to be rescheduled to other dates, you will not be eligible for any refunds. In case the event is fully cancelled, the refund, if issued, will include ONLY the face value/printed value of the ticket(s). The bearer cannot, under any circumstances, claim refunds on shipping fees, processing fees, handling fees, and other charges.</p>
<p></p>
<p>Losses, if any, occurring due to foreign exchange fluctuations at the time of refunds have to be borne by the purchaser. Cmarketing Inc. cannot be, in any way, held liable for that.</p>
<p></p>
<p>All ticket sales are deemed as FINAL TRANSACTIONS. There will be no ticket returns/exchanges/cancellations.</p>
<p></p>
<p>The Small Business Summit 2025 is a ‘rain or shine’ event. By purchasing the event tickets, you automatically confirm that you/the attendee(s) are of the ‘minimum age’ or older, at the time of buying the ticket(s). The ‘minimum age’ for attending The Small Business Summit 2025 is 16.</p>
<p></p>
<p>If it is proven beyond doubt at the venue that an attendee is below the ‘minimum age’, (s)he will not be granted entry to The Small Business Summit 2025. No refunds, full or partial, will be issued either.</p>
<p></p>
<p>At the time of entering the event venue, you are required to produce the ticket and a valid identification document (ID). By presenting an ID, you confirm that all details present on it are accurate and updated.</p>
<p></p>
<p>By purchasing The Small Business Summit 2025 tickets and presenting the same at the venue, you give your consent to Cmarketing Inc. to collect certain information about yourself (name, picture, date of birth, gender, address, etc.) for verification and storage.</p>
<p></p>
<p>You hereby agree to forego all types of surcharge claims – full or in part – and all claims &amp; entitlements related to it.</p>
<p></p>
<p>Cmarketing Inc. does not take any responsibility for personal/financial damages caused to you at the venue or for any items lost/stolen/misplaced at the venue. Financial reimbursement claims on these counts will not be entertained.</p>
<p></p>
<p>The Small Business Summit 2025 and Cmarketing Inc. follow a common ‘zero-tolerance policy’ towards unauthorized drug usage or carrying at the venue, AND towards any type of behaviour/actions that can be interpreted as lewd or obscene. If you are found in possession of prohibited items at The Small Business Summit 2024 and/or if you indulge in any objectionable action, your participation in the event will be terminated immediately. Your event ticket(s) will become void from that point on.</p>
<p></p>
<p>Please note that you purchase The Small Business Summit 2025 tickets of your own free will. As such, you do not have the right to initiate a chargeback claim or dispute with the provider of the credit card/debit card that had been used for the transaction. Further refund/return/cancellation requests will not be entertained either.</p>
<p></p>
<p>In all disputes between the ticket-bearer and the ticket-issuer, the latter’s decision will be deemed final. If a dispute cannot be resolved, the help of an independent third-party arbiter will be sought.</p>
<p></p>
<p>We really hope you will have a great time at The Small Business Summit 2025. You can get your tickets here</p>
<p>www.smesummit.ca.</p>
<p></p>
<p><strong>Disclaimer:</strong></p>
<p><strong>Event Information:</strong> All information regarding the CanadianSME Small Business Summit 2025, including but not limited to the dates, venues, program schedule, speakers, exhibitors, and participants, is subject to change without prior notice.</p>
<p><strong>Limitation of Liability:</strong> The organizers of the CanadianSME Small Business Summit 2025 will not be liable for any direct, indirect, consequential, or incidental damages, including but not limited to, damages for loss of profits, goodwill, use, data, or other intangible losses resulting from (i) your participation in or inability to participate in the event; (ii) unauthorized access to or alteration of your transmissions or data; (iii) statements or conduct of any third party at the event; or (iv) any other matter relating to the event.</p>
<p><strong>Intellectual Property:</strong> All content and materials presented at the CanadianSME Small Business Summit 2025 are owned by their respective owners and are protected by applicable intellectual property and copyright laws. No content or materials from the event may be copied, reproduced, republished, uploaded, posted, transmitted, or distributed in any way without explicit permission from the respective rights holders.</p>
<p><strong>Use of Event Recordings:</strong> The CanadianSME Small Business Summit 2025 organizers reserve the right to record any part of the event, including speakers, panel discussions, workshops, keynotes, demonstration sessions, and exhibitor booths. These recordings may be used for promotional purposes, social media postings, and other marketing endeavors.</p>
<p><strong>Photography &amp; Videography Disclaimer</strong></p>
<p>By attending the CanadianSME Small Business Show and/or the CanadianSME Small Business Summit, you acknowledge and agree to the following:</p>
<p>During the event, photographs and videos will be captured for promotional, marketing, and archival purposes.</p>
<p>These images and recordings may include attendees, exhibitors, speakers, and sponsors and may be taken in front of our media wall, within session rooms, on the exhibition floor, and other event areas.</p>
<p>By entering the event premises, you consent to being photographed, filmed, and/or otherwise recorded, and to the release, publication, exhibition, or reproduction of such media for use in:</p>
<ul>
    <li>News,</li>
    <li>Webcasts,</li>
    <li>Promotional materials,</li>
    <li>Social media,</li>
    <li>Advertising,</li>
    <li>and any other purpose by CanadianSME and its affiliates.</li>
</ul>
<p>You waive any right to inspect or approve the use of any photo or video that may be taken, and you understand that all media will remain the property of CanadianSME.</p>
<p>If you do not wish to be recorded or photographed, please inform the registration desk upon arrival and notify the media team on-site. We will do our best to accommodate your request, though we cannot guarantee complete exclusion from all event recordings or photos.</p>
<p><strong>Health and Safety:</strong> The organizers of the CanadianSME Small Business Summit 2025 cannot be held responsible for the health and safety of participants during the event. Participants are expected to adhere to any and all public health guidelines and regulations in place at the time of the event.</p>
<p><strong>Views Expressed:</strong> The views and opinions expressed by any individuals at the event (including speakers, exhibitors, and attendees) are those of the individuals themselves and do not necessarily reflect the official policy or position of the CanadianSME Small Business Summit 2025 or its organizers.</p>
<p>This disclaimer is to be read and fully understood before using our site, joining our email list, or participating in the event. By partaking in the CanadianSME Small Business Summit 2025, you agree to these terms and conditions.</p>
";
        DB::table('settings')->insert([
            [
                'key' => 'terms_conditions',
                'value' => $terms_conditions,
            ],
        ]);

        Page::where('slug','terms')->update(['description' => $terms_conditions]);




        // Default Thank You Page Message
        DB::table('settings')->insert([
            [
                'key' => 'thank_you_page',
                'value' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
        ]);

        // Default Thank You Page Message
        $about = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
        DB::table('settings')->insert([
            [
                'key' => 'about',
                'value' => $about,
            ],
        ]);
        Page::where('slug','about')->update(['description' => $about]);
 

        $support = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
        DB::table('settings')->insert([
            [
                'key' => 'support',
                'value' => $support,
            ],
        ]);

        Page::updateOrCreate(['slug' => "support"], ['description' => $support]);
    }
}


