<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Undergraduate Transfer Credentials</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 10px;
            line-height: 1.2;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        .section { margin-bottom: 10px; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .small { font-size: 9px; }
        .field-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            margin-bottom: 4px;
        }
        .spacer { height: 5px; }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .col {
            width: 48%;
            text-align: center;
        }
        header img, footer img {
            max-height: 80px; /* reduce header/footer height */
            width: 100%;
            height: auto;
            object-fit: contain;
        }
    </style>
</head>
@php
    // Use local file path with file:// protocol for DomPDF
    $headerImagePath = public_path('images/header.png');
    if (!file_exists($headerImagePath)) {
        $headerImagePath = public_path('images/header.jpg');
    }
    $headerImage = 'file://' . $headerImagePath;

    $footerImagePath = public_path('images/footer.png');
    if (!file_exists($footerImagePath)) {
        $footerImagePath = public_path('images/footer.jpg');
    }
    $footerImage = 'file://' . $footerImagePath;
@endphp
<body>

    <header>
        <img src="{{ $headerImage }}" alt="Header Image">
    </header>

    <div class="section">
        <h2 class="center bold">OFFICE OF THE REGISTRAR</h2>
        <p class="small right"><em>TC PSU-UC No. {{ $tcno }} {{ $year }}</em></p>
        <p>TO WHOM IT MAY CONCERN:</p>
        <p>
            This is to certify that <span class=" underline">{{ $name }}</span> of 
            <span class=" underline">{{ $address }}</span>, a <span class="underline">{{ $sy }}</span> year <span class="underline"> {{ $program }} </span> of this university, 
            is hereby granted <span class="">TRANSFER CREDENTIALS</span> this 
            <span class="">{{ $day }}</span> day of 
            <span class="">{{ $month }}</span>, 
            <span class="">{{ $year }}</span>.
        </p>
        <p>
            Transcript of Record of the above student will be forwarded upon the request of the school where he/she transferred.
        </p>

        <div class="spacer"></div>

        <div class="right">
            <p class="bold underline">{{ $registrarName }}</p>
            <p style="margin-right: 60px;">Registrar I</p>
        </div>

        <p class="small"><em>NOT VALID WITHOUT <br> COLLEGE SEAL</em></p>
    </div>
        <hr >
    <div class="section">
        <div class="center">
            <p>_______________________________________________ <br> Name of School/College/University</p>
            <p>_______________________________________________ <br> Address</p>
            
            <div class="right">
                <p>_________________</p>
                <p style="margin-right: 60px;"> Date</p>
            </div>
        </div>
        <br>
        <p>The Registrar<br>Pangasinan State University<br>Urdaneta Campus, Urdaneta City</p>
        <p>Sir/Madam:</p>
        <p>
            Please furnish us with the Official Transcript of Records of 
            <span class="bold underline">{{ $name }}</span> who has been enrolled in this school upon presentation of his/her 
            TRANSFER CREDENTIALS dated 
            <span class="bold underline">{{ $day }}</span> of <span class="bold underline">{{ $month }}</span>, 
            <span class="bold underline">{{ $year }}</span>.
        </p>

                <div class="row">
            <div class="col">
                <p>WITH MY CONSENT:</p>
            </div>
            <div class="col"></div>
            <div class="col">
                <p class="bold underline field-line">&nbsp;</p>
                <p>Requesting Officer</p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <p class="bold underline field-line">&nbsp;</p>
                <p>Student Signature</p>
            </div>
            <div class="col"></div>
            <div class="col">
                <p class="bold underline field-line">&nbsp;</p>
                <p>Title</p>
            </div>
        </div>
        <p class="small">Email #: _____________________</p>
    </div>

    <footer>
        <img src="{{ $footerImage }}" alt="Footer Image">
        <p class="small right"><em><strong>Note: This document is issued only once. Please return it if unused.</strong></em></p>
    </footer>

</body>
</html>
