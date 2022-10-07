<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
    <script src="{{ $izipay_client->getClientEndpoint() }}/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js" kr-public-key="{{ $izipay_client->getPublicKey() }}" kr-post-url-success="/api/izipay_finished"></script>
    <script src="{{ $izipay_client->getClientEndpoint() }}/static/js/krypton-client/V4.0/ext/classic.js"></script>
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <style>
        * {
        margin:0;
        padding:0;
        font-family: 'Roboto Mono', monospace;
        }
        .home-background {
        background-image: url('bg-f.jpg');  
        position: relative;
        display: block;
        overflow-x: hidden;
        background-size: contain;
        background-attachment: fixed;
        height: 100vh;
        background-color: rgba(22, 23, 36 ,1);

        background-position: center top;
        background-size: 100%;
        background-repeat: no-repeat;
        }
        .home-background::before {
        content: "";
        position: absolute;
        background-color: rgba(0,0,0,.25);
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        }
        .home-background h1{
        text-align: center;
        font-weight: 600;
        color: #fff;
        margin-top: 2rem;
        position: relative;
        text-shadow: 2px 2px 4px #000000;
        }
        .return {
        position: relative;
        z-index: 99;
        background-color: #000;
        padding: 1rem;
        }
        .checkout-return {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0.8em 1.6em;
        border: none;
        border-radius: 12px;
        transition: background-color .5s linear;
        font-size: 18px;
        margin: 3px 10px;
        width: 100px;
        background-color: #b6ff40;
        border: 2px solid #b6ff40;

        }
        .checkout-return p {
        color: #0e0d0d;
        }
        .checkout-return:hover {
        background-color: transparent;

        }
        .checkout-return:hover p {
        color: #b6ff40;
        }
        .kr-embedded {
        padding:50px!important;
        display: flex;
        flex-direction: column;
        max-width: 500px;
        margin: 0 auto;
        align-items: center;
        overflow:auto;
        }
        .a {
        display: flex;
        min-width: 460px;
        }
        .b {
        background: rgb(120,135,176);
        background: linear-gradient(90deg, rgba(120,135,176,1) 0%, rgba(87,108,152,1) 51%);

        padding: 3rem 1rem 1rem;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        position: relative;
        }
        .c {
        background-color: #7182ad;
        position: relative;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        width: 125px;
        }
        .b p{
        position: absolute;
        left: 55%;
        top: 52%;
        width: 40px;
        font-size: 10px;
        line-height: 12px;
        font-size: 10px;
        color: rgba(255,255,255,.6);
        }
        .kr-installment-number {
        visibility: hidden!important;
        }
        .kr-input-field {
        color: #fff!important;
        }
        .kr-input-field::placeholder {
        color: #999!important;
        }
        .padding-re {
        padding: 1rem 2rem;
        }
        .black-rect {
        background-color: rgba(0, 0, 0, 0.7);
        width: 100%;
        height: 50px;
        margin-top: 38px;
        }
        .kr-first-installment-delay {
        display: none!important;
        }
        .kr-pan {
        margin-bottom: 1rem;  
        border-radius: 2px;
        }
        .kr-visibility-button {
        position: absolute;
        right: -26px;
        top: -15px;
        filter: brightness(0%) invert(100%);
        -webkit-filter: brightness(0%) invert(100%);
        -moz-filter: brightness(0%) invert(100%);
        }
        input {
        font-size: 14px!important;
        padding: 10px!important;
        width: 93%!important;
        border-radius: 2px!important;
        color: black!important;
        }
        iframe {
        height: 36px!important;
        }
        input:focus {
        outline: none !important;  
        box-shadow: 0 0 10px #719ECE;
        }
        .kr-expiry {
        background-color: #5373ad;

        border-radius: 2px;
        width: 80px;
        float: right;
        margin-bottom: 15px;
        font-size: 14px;
        }
        .kr-field-component { 
        background-color: #5373ad;
        font-size: 14px;
        border-radius: 2px;
        }
        .kr-payment-button {
        background-color:#B6FF40!important;
        border-radius: 8px;
        padding: 1rem 4rem;
        transition: all .3s ease;
        width: 80%;
        border-color:transparent;
        height: 54px;
        }
        .kr-payment-button:hover {
        background-color:rgba(182, 255, 64, .8)!important;
        }
        .padding-re p {
        color: rgba(255,255,255,.6); 
        position: absolute;
        bottom: 0;
        right: 0;
        margin-bottom: 8px;
        line-height: 12px;
        font-size: 10px;
        text-align: center;
        }
        @media screen and (max-width: 485px) {
        .kr-embedded {
        align-items: flex-start;
        padding: 50px 0!important;
        }
        }
    </style>
</head>
<body>
    <div class='home-background'>
        <div class="return">
            <a href='http://apuestadota.com/start' class='checkout-return'>
                <img src='/images/chevron-left.png' alt='return' />
                <p>Volver</p>
            </a>
        </div>
        <h1> Ingresa tu tarjeta para proceder con el pago </h1>
        <div class="kr-embedded" kr-form-token="{{ $token }}">
            <div class='a'> 
                <div class='b'>
                    <div class="kr-pan"></div>
                    <p>VALIDO HASTA</p>
                    <div class="kr-expiry"></div>
                    <div class="kr-card-holder-name"></div>
                </div>
                <div class='c'> 
                    <div class='black-rect'></div>
                    <div class='padding-re'>
                    <div class="kr-security-code"></div> 
                    <p>Los tres ultimos digitos en el reverso</p>    
                    </div>
                </div>
            </div>
            <button class="kr-payment-button"></button>
            <div class="kr-form-error"></div>
        </div>  
    </div>
</body>
</html>