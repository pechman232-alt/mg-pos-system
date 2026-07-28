<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Not Activated - MG CAMBODIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Khmer+OS+Battambang:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Strict reset for perfect screen fitting */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        /* Forces the container to fit laptop and monitor screens perfectly */
        body { 
            background-color: #0f0f11; 
            color: #ffffff; 
            font-family: 'Khmer OS Battambang', 'Segoe UI', Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            overflow: hidden; 
        }
        
        /* Clean, professional alert card */
        .alert-card { 
            background: #1a1a1c;
            border-radius: 12px;
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            border: 1px solid #2a2a2c;
            border-top: 4px solid #b21e27; /* Standard MG Red */
        }

        .icon-box { 
            background-color: rgba(178, 30, 39, 0.08); 
            border-radius: 50%; 
            width: 72px; 
            height: 72px; 
            margin: 0 auto 24px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            border: 1px solid rgba(178, 30, 39, 0.2); 
        }

        .icon-box svg { 
            fill: #e63946; 
            width: 36px; 
            height: 36px; 
        }

        h2 { 
            font-size: 22px; 
            font-weight: 700;
            margin-bottom: 12px; 
            color: #f8f9fa;
        }

        p { 
            color: #8b8f97; 
            font-size: 15px; 
            line-height: 1.6;
            margin-bottom: 32px; 
        }

        /* Minimalist full-width button */
        .btn-contact { 
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff; 
            color: #000000; 
            padding: 14px 28px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 15px; 
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-contact:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="alert-card">
        <div class="icon-box">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
        </div>
        <h2>កាតមិនទាន់ដំណើរការទេ!</h2>
        <p>កាតសមាជិកមួយនេះ មិនទាន់ត្រូវបានភ្ជាប់ទៅនឹងកុងត្រាអតិថិជនណាមួយនៅក្នុងប្រព័ន្ធនៅឡើយទេ។</p>
        <a href="tel:012345678" class="btn-contact">📞 ទាក់ទងផ្នែកលក់ (Contact Sales)</a>
    </div>
</body>
</html>