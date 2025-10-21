<?php
namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;


class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::latest()->paginate(10);
        return view('badges.index', compact('badges'));
    }

    public function create()
    {
        return view('badges.create');
    }

    public function store(Request $request)
    {  
        $request->validate([
            'selected_fields' => 'required|array|min:1',
            'selected_fields.*' => 'in:name,company_name,designation,logo,qr_code',
            'badge_name' => 'required|string|max:255',
            'width'=> 'required|numeric|between:0,999.99|regex:/^\d+(\.\d{1,2})?$/',
            'height'=> 'required|numeric|between:0,999.99|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $badge = new Badge();
        $badge->badge_name = $request->badge_name;
        $badge->selected_fields = $request->selected_fields;

        // Handle optional fields based on selection
        if (in_array('name', $request->selected_fields)) {
            $badge->name = 1;
        }

        if (in_array('company_name', $request->selected_fields)) {
            $badge->company_name = 1;
        }

        if (in_array('designation', $request->selected_fields)) {
            $badge->designation = 1;
        }

        // Handle logo upload
        if (in_array('logo', $request->selected_fields)) {
            $badge->logo_path = 1;
        }

        // // Handle QR code generation
        if (in_array('qr_code', $request->selected_fields)) {
            $badge->qr_code_data = 1;
        }
        $badge->width =$request->width ;
        $badge->height =$request->height ;

        $badge->save();

        return redirect()->route('badges.show', $badge)
                         ->with('success', 'Badge generated successfully!');
    }

    public function show(Badge $badge)
    {
        return view('badges.show', compact('badge'));
    }

    public function download(Badge $badge)
    {
        if ($badge->badge_path && Storage::disk('public')->exists($badge->badge_path)) {
            return Storage::disk('public')->download($badge->badge_path);
        }

        return redirect()->back()->with('error', 'Badge file not found.');
    }

    private function generateQrCode($data)
    {
        $filename = 'qr_codes/qr_' . uniqid() . '.png';
        $qrCode = QrCode::format('png')->size(200)->generate($data);
        
        Storage::disk('public')->put($filename, $qrCode);
        
        return $filename;
    }

    private function generateBadgeImage(Badge $badge)
    {
        // Create a blank badge canvas (you can customize dimensions)
        $width = 600;
        $height = 400;
        
        $img = Image::canvas($width, $height, '#ffffff');
        
        // Add border
        $img->rectangle(10, 10, $width - 10, $height - 10, function ($draw) {
            $draw->border(3, '#333333');
        });

        $yPosition = 50;
        $lineHeight = 40;

        // Add selected fields to the badge
        $selectedFields = $badge->selected_fields;

        // Add logo if selected
        if (in_array('logo', $selectedFields) && $badge->logo_path) {
            $logoPath = storage_path('app/public/' . $badge->logo_path);
            if (file_exists($logoPath)) {
                $logo = Image::make($logoPath)->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->insert($logo, 'top-left', 20, 20);
            }
        }

        // Add name if selected
        if (in_array('name', $selectedFields) && $badge->name) {
            $img->text($badge->name, $width / 2, $yPosition, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(24);
                $font->color('#000000');
                $font->align('center');
                $font->valign('top');
            });
            $yPosition += $lineHeight;
        }

        // Add company name if selected
        if (in_array('company_name', $selectedFields) && $badge->company_name) {
            $img->text($badge->company_name, $width / 2, $yPosition, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(18);
                $font->color('#666666');
                $font->align('center');
                $font->valign('top');
            });
            $yPosition += $lineHeight;
        }

        // Add designation if selected
        if (in_array('designation', $selectedFields) && $badge->designation) {
            $img->text($badge->designation, $width / 2, $yPosition, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(16);
                $font->color('#888888');
                $font->align('center');
                $font->valign('top');
            });
            $yPosition += $lineHeight;
        }

        // Add QR code if selected
        if (in_array('qr_code', $selectedFields) && $badge->qr_code_path) {
            $qrPath = storage_path('app/public/' . $badge->qr_code_path);
            if (file_exists($qrPath)) {
                $qrCode = Image::make($qrPath)->resize(120, 120);
                $img->insert($qrCode, 'bottom-right', 20, 20);
            }
        }

        // Save the badge
        $filename = 'badges/badge_' . $badge->id . '_' . uniqid() . '.png';
        $img->save(storage_path('app/public/' . $filename));

        return $filename;
    }

    public function generateBadges(Request $request)
    {   
       
        //$badge = Badge::where('badge_name',$request->template_name)->first();
        $badge = Badge::latest()->first();
        $badges = [];
        $userIds = json_decode($request->user_ids, true);
        $users = User::whereIn('id', $userIds)->get();
        
        foreach($users as $user){
        $name=''; 
        if($badge->name == 1){
            $name = $user->full_name ?? '';
        } 
        $company_name='';
        if($badge->company_name == 1){
            $company_name = $user->company ?? '';
        } 

        $logo = '';
        if($badge->logo_path == 1){
            $logo = 1;
        } 

        $qr_code ='';
        if($badge->qr_code_data == 1){
           $qr_code = $user->qr_code ? asset($user->qr_code) : '';
        }    
        
        $designation ='';
        if($badge->designation == 1){
           $designation = $user->designation ?? '';
        } 
        
        array_push($badges,
            [
                'name' => $name,
                'company_name' => $company_name ,
                'logo' => $logo, 
                'qr_code' => $qr_code,
                'designation'=>$designation,
                'width'=>$badge->width ?? '8.56',
                'height'=>$badge->height ?? '5.40',
                'user_id' => $user->id ??''
            ]
        );
           
    }
 
    return view('badges.pdf',compact('badges'));          
  }

    public function destroy(Badge $badge)
    {   
        $badge->delete();
        return redirect()->route('badges.index')->with('success', 'Badge deleted.');
    }
}