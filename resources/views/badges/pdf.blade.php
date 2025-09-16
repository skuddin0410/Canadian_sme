<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Badges</title>
</head>
<body>
    <div style="width: 100%; max-width: 1300px; margin: 0 auto; padding: 20px;">
    	 <button onclick="window.print()" style="margin-bottom: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
            Print Badges
        </button>
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
            @foreach($badges as $badge)
            <div class="badge-container" style="width: 23%; margin-bottom: 15px; box-sizing: border-box;">
                <div class="badge" style="width: 100%; height: 200px; border: 2px solid #333; border-radius: 10px; padding: 15px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); display: flex; justify-content: space-between; align-items: center;">
                    
                    <!-- Left Section: Name, Company, Designation -->
                    <div class="left-section" style="display: flex; flex-direction: column; justify-content: left; width: 60%; text-align: left;">
                        <!-- Logo (uncomment if needed) -->
                        <img src="{{asset('sme-logo.png')}}" style="max-height: 100px; max-width: 100px; object-fit: contain; border-radius: 8px; margin-bottom: 5px;">
                        <!-- Name -->
                        <p class="name" style="font-weight: bold; font-size: 1rem; margin: 0;">Name</p>
                        <!-- Company Name -->
                        <p class="company" style="font-size: 0.9rem; color: #555; margin: 0;">company_name</p>
                        <!-- Designation -->
                        <p class="designation" style="font-size: 0.8rem; color: #888; margin: 0; font-style: italic;">Designation</p>
                    </div>

                    <!-- Right Section: QR Code -->
                    <div class="qr-code" style="width: 35%; display: flex; justify-content: center; align-items: center;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=Sample-QR-Code" style="width: 120px; height: 120px; object-fit: contain;">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
     <script>
        // Optional: You can add a function to trigger the print when needed
        function printPage() {
            window.print();
        }
    </script>
</body>
</html>

