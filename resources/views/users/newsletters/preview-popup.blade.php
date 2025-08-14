<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $newsletter->subject }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Inter', system-ui, sans-serif;
            background: #f8fafc;
        }
        
        .email-preview {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            text-align: center;
        }
        
        .device-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 8px;
        }
        
        .mobile-view {
            max-width: 375px;
            margin: 0 auto;
        }
        
        .desktop-view {
            max-width: 600px;
            margin: 0 auto;
        }
        
        @media (max-width: 640px) {
            .device-toggle {
                position: relative;
                top: auto;
                right: auto;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body class="p-4">
    <!-- Device Toggle -->
    <div class="device-toggle">
        <div class="flex bg-gray-100 rounded-lg p-1">
            <button onclick="setView('desktop')" 
                    class="px-3 py-1 text-sm rounded-md transition-all duration-200 desktop-btn">
                💻 Desktop
            </button>
            <button onclick="setView('mobile')" 
                    class="px-3 py-1 text-sm rounded-md transition-all duration-200 mobile-btn">
                📱 Mobile
            </button>
        </div>
    </div>

    <!-- Preview Container -->
    <div class="min-h-screen flex items-center justify-center py-8">
        <div id="preview-container" class="desktop-view transition-all duration-300">
            <div class="preview-header">
                <h1 class="text-lg font-bold">📧 Email Preview</h1>
                <p class="text-sm opacity-90">{{ $newsletter->subject }}</p>
            </div>
            <div class="email-preview">
                @include('emails.newsletters.' . ($newsletter->template_name ?: 'default'), [
                    'newsletter' => $newsletter,
                    'content' => $newsletter->content,
                    'templateData' => $newsletter->template_data ?? [],
                    'trackingPixelUrl' => '#tracking-pixel',
                    'unsubscribeUrl' => '#unsubscribe',
                    'recipientEmail' => 'preview@example.com'
                ])
            </div>
            
            <!-- Preview Actions -->
            <div class="bg-white p-4 text-center border-t">
                <div class="flex justify-center space-x-3">
                    <button onclick="window.close()" 
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm">
                        Close Preview
                    </button>
                    <button onclick="window.print()" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm">
                        Print
                    </button>
                    @if($newsletter->canBeSent())
                    <a href="{{ route('admin.newsletters.edit', $newsletter) }}" target="_blank"
                       class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 text-sm">
                        Edit Newsletter
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function setView(viewType) {
            const container = document.getElementById('preview-container');
            const desktopBtn = document.querySelector('.desktop-btn');
            const mobileBtn = document.querySelector('.mobile-btn');
            
            // Reset classes
            container.classList.remove('desktop-view', 'mobile-view');
            desktopBtn.classList.remove('bg-blue-500', 'text-white');
            mobileBtn.classList.remove('bg-blue-500', 'text-white');
            
            // Apply new view
            container.classList.add(viewType + '-view');
            
            if (viewType === 'desktop') {
                desktopBtn.classList.add('bg-blue-500', 'text-white');
            } else {
                mobileBtn.classList.add('bg-blue-500', 'text-white');
            }
        }
        
        // Initialize desktop view
        document.addEventListener('DOMContentLoaded', function() {
            setView('desktop');
        });
        
        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>