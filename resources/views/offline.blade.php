<!DOCTYPE html>
<html>
<head>
    <title>Offline</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .offline-container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h1 {
            color: #666;
            margin-bottom: 20px;
        }
        p {
            color: #999;
            margin-bottom: 30px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="icon">📶</div>
        <h1>You're Offline</h1>
        <p>Please check your internet connection and try again.</p>
        <button onclick="window.location.reload()">Retry</button>
    </div>
</body>
</html>
