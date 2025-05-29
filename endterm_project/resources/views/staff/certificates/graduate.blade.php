<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Graduate Transfer Credentials</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.3;
            font-size: 11px;
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
        .spacer { height: 20px; }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .col {
            width: 50%;
            text-align: center;
        }
        header img, footer img {
            max-height: 90px; /* reduce header/footer height */
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

    <!-- HEADER IMAGE -->
    <header>
        <!-- the src must change in subject to what file type is saved in the public/images/header.*, same for the footer below -->
        <img src="{{ $headerImage }}" alt="Header Image">
    </header>

    <div style="padding: 20px;" class="section">
        <p class="small right"><em>TC PSU-UC No. {{ $tcno }} s.{{ $year }}</em></p>
        <p>TO WHOM IT MAY CONCERN:</p>
        <p>
            This is to certify that <span class="bold underline">{{ $name }}</span> of 
            <span class="bold underline">{{ $address }}</span>, graduate of this university, 
            is hereby granted <span class="bold">TRANSFER CREDENTIALS</span> this 
            <span class="bold">{{ $day }}</span> day of 
            <span class="bold">{{ $month }}</span>, 
            <span class="bold">{{ $year }}</span>.
        </p>
        <p>
            Transcript of Record of the above student will be forwarded upon the request of the school where he/she transferred.
        </p>

        <div class="spacer"></div>

        <div class="right">
            <p class="bold underline">{{ $registrarName }}</p>
            <p style="margin-right: 60px;">Registrar</p>
        </div>

        <p class="small"><em>NOT VALID WITHOUT <br> UNIVERSITY SEAL</em></p>
    </div>

    <div class="section">
        <div class="center">
            <p>_______________________________________________ <br> Name of School/College/University</p>
            <p>_______________________________________________ <br> Address</p>
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
        <p class="bold">Contact #: _____________________</p>
    </div>
    <p class="small bold right"><em>Note: This document is issued only once. Please return it if unused.</em></p>
    <!-- FOOTER IMAGE -->
    <footer>
        <img src="{{ $footerImage }}" alt="Footer Image">
        
    </footer>

</body>
</html>
