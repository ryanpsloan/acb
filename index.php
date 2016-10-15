<?php
//phpinfo();
session_start();
/**********************************************************************************************************************
Author: Ryan Sloan
This page acts as the starting point for the upload and processing of LCN F657 General Ledger files. It takes a .txt.
After upload the form action sends the file data to processor.php
ryan@paydayinc.com
 *********************************************************************************************************************/

if(isset($_SESSION['creditFileName'])){
    $downloadCredit = '<a href="downloadCredit.php">Download Credit File</a>';
    $clear = '<a href="clear.php">Clear Files</a>';
}
else{
    $downloadCredit = "";
    $clear = "";
}

if(isset($_SESSION['debitFileName'])){
    $downloadDebit = '<a href="downloadDebit.php">Download Debit File</a>';
    $clear = '<a href="clear.php">Clear Files</a>';
}
else{
    $downloadDebit = "";
    $clear = "";
}

if(isset($_SESSION['exceptionsFileName'])){
    $downloadExceptions = '<a href="downloadExceptions.php">Download Exceptions File</a>';

}else{
    $downloadExceptions = '';
}

?>
<!DOCTYPE>
<html>
<head>
    <title>ACB File Creator</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <style>
        body{
            background-color: lightblue;
        }
        table{
            margin-left: auto;
            margin-right: auto;
        }
        td{
            padding: 3px 0px 3px 0px;
        }
        .container-fluid{
            display: block;
        }
        #instructionsDiv{
            text-align: center;
        }
        #actionDiv{
            display: inline-block;
            height: 40%;
            width: 100%;
            padding: 15px;
            text-align: center;

        }
        #containerDiv{
            position: absolute;
            border: 1px solid lightslategrey;
            top: 3%;
            left: 38%;
            height: 22em;
            width: 27em;
            margin-left: auto;
            margin-right: auto;
            padding: 18px;
        }
        #resultsDiv{
            text-align: center;
            width: 100%;
            /*border: 1px solid blue;*/
            margin-left: auto;
            margin-right: auto;
            padding: 15px;
        }

        .button{
            background-color: royalblue;
        }
        .heading{
            font-weight: bold;
            font-size: 18px;
        }
        .red{
            color: red;
        }
        .green{
            color: green;
        }
        .highlight{
            background-color: cadetblue;
            padding: 3px;
        }
        .border{
            text-decoration: underline;
        }
        .balanceDoc{
            text-decoration: none;
        }
        .link{
            color: blue;
        }

    </style>
    <script>
        function openWindow() {
            window.open("http://10.162.12.93/lcdn/instructions.html", "_blank", "toolbar =yes, scrollbars=yes, " +
                "resizable=yes, top=100, left=100, width=850, height=500");
        }
    </script>
</head>
<body>
<header>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Home</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><?php echo $downloadCredit; ?></li>
                    <li><?php echo $downloadDebit; ?></li>
                    <li><?php echo $downloadExceptions; ?></li>

                </ul>

                <ul class="nav navbar-nav navbar-right">

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>
<main>
    <div class="container-fluid">
        <div class="row">
            <div id="instructionsDiv" class="col-md-12">
                <table>
                    <tr><th><h2>ACB File Converter</h2></th></tr>
                    <tr><th>Instructions:</th></tr>
                    <tr><td>1. Run file from ACB website. Columns to select: Date,Credit Amount, Debit Amount, Code, x2ndReference, and Memo </td></tr>
                    <tr><td>2. Upload file and click Create Files</td></tr>
                    <tr><td>3. Download Formatted Credit and Debit Files</td></tr>
                </table>

            </div>
        </div>
        <div class="row">
            <div id="actionDiv" class="col-md-12">
                <div id="containerDiv" class="col-md-4">
                    <form action="processor.php" method="POST" enctype="multipart/form-data">
                        <table>
                            <tr><td id="header"><label for="file">Upload unsorted .txt</label></td></tr>
                            <tr><td><input type="file" id="file" name="file"></td></tr>
                            <tr><td><input type="submit" value="Create Files" class="button"></td></tr>
                            <tr><td><?php echo $clear;?></td></tr>

                            <tr><td><?php if(isset($_SESSION['output'])){echo $_SESSION['output'];} ?></td></tr>

                        </table>

                    </form>

                </div>
            </div>
        </div>



    </div>
</main>
</body>
</html>