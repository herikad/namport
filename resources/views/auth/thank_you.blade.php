<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Thank You</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #2c2f3a;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      display: flex;
      background-color: #2c2f3a;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
      max-width: 900px;
    }

    .image {
    background: url('https://supermia.ai/wp-content/uploads/2025/02/homepagebanner.png') no-repeat center center;
    background-size: cover;
    width: 700px;
    min-height: 400px;
    }
    .content {
      padding: 40px;
      width: 50%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background-color: #373b48;
    }

    .content h1 {
      font-size: 28px;
      margin-bottom: 15px;
      color: #fff;
    }

    .content h1 span {
      color: #ffce00;
    }

    .content p {
      font-size: 16px;
      color: #ccc;
      margin-bottom: 30px;
    }

    .content a {
      display: inline-block;
      background-color: #ffce00;
      color: #000;
      padding: 12px 25px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 600;
    }

    .content a:hover {
      background-color: #f5c400;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image"></div>
    <div class="content">
      <h1>Thank you For <span>submitting</span></h1>
      <a href="{{route('login')}}">Back to Login...</a>
    </div>
  </div>
</body>
</html>
