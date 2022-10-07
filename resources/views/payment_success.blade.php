<!DOCTYPE html>
<html lang="es">
<head>
  <meta name="viewport" 
   content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: 'Roboto Mono', monospace;
      margin: 0;
      padding: 0;
      box-sizing: border-box; 
    }
      .home-background {
        background-image: url('bg-f.jpg');  
        position: relative;
        display: block;
        overflow-x: hidden;
        background-size: contain;
        background-attachment: fixed;
        height: 100vh;
      }
      .home-background::before {
        content: "";
        position: absolute;
        background-color: rgba(0,0,0,.35);
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
      }

      .text {
        position: relative;
        margin: 0 auto;
        max-width: 1200px;
        padding: 2rem;
      }

      .logo {
        margin-left: 44%;
        height: 140px;

      }
      .anchor-return {
        position: absolute;
        color: rgba(255, 255, 255, .7);
        transition: all .3s ease;   
        text-decoration: none;
        left: 0;
      }
      .anchor-return:hover {
        color: rgba(255, 255, 255, 1);
      }
      h3 {
        color: #fff;
        font-size: 1.7rem;
        text-align: center;
        letter-spacing: -2px;
      }
      @media screen and (max-width: 485px) {
        .home-background::before {
          height: 125%;
        }
        .logo {
          margin-left: 55%;
        }
        .anchor-return {
          padding: 0;
        }
       
        .anchor-return p {
          font-size: 1.4rem;
          top: 35%;
          left:35%;
        }
        .amount {
          left: 33%;
        }
        .anchor-download {
          bottom: -10%;
          right: 90px;
        }
      }

  </style>
</head> 
<body>
  <div class='home-background'>
    <div class='text'>
      <a class="anchor-return" href="http://apuestadota.com/start">
        <p>Volver</p>
      </a> 
      <div class="flex-container"> 
        <h3> Gracias por tu recarga, en breve podras verificar tu saldo en tu cuenta.</h3>
      </div>
    </div>
  </div>
</body>   
</html> 