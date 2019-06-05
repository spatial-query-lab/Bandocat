<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
$pageContents = "";

// Getting parameter
if(isset($_GET["option"]))
{
    $option = $_GET["option"];

    $userRole = $session -> getRole();
    //If the user is a reader automatically redirect them to the edit page because only one button remains, and is pointless
    //to have 1 button to go to one place.
    if($userRole == "Reader")
    {
        header('Location: ./list.php?col='.$collection.'&action=review');
    }
}

else
{
    header("Location: ../../");
}

// Switching between options
switch($option)
{
    // All collections
    case "Catalog":
        $pageContents = "./catalog.php";
        break;

    case "Edit/View":
        // All collections
        $pageContents = "./edit_view.php";
        break;

    case "Rectify":
        // Blucher, Green, Penny, Job
        $pageContents = "./rectify.php";
        break;

    case "Upload":
        // Field Book
        $pageContents = "./upload.php";
        break;

    case "Transcribe":
        // Map Indices, Field Book Indices
        $pageContents = "./transcribe.php";
        break;

    default:
        header("Location: ../../");
}
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title><?php echo $option; ?> Menu</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body style="background-color: #f1f1f1; background-size: 80% 80%; background-repeat: no-repeat; background-position: center;" background="../../Images/blueFade-world-map.png">
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container">
    <div class="row" >
        <div class="col">
            <!-- Put Page Contents Here -->
            <h1 class="text-center"><?php echo $option; ?></h1>
            <hr>
            <div style="text-align: center;">
                <h5 class="text-center">Please Select a Collection</h5>
                <div class="d-flex justify-content-center">
                    <?php include "$pageContents"; ?>
                </div>
            </div>
        </div>
    </div> <!-- row -->
</div><!-- Container -->
<?php include "../../Master/bandocat_footer.php" ?>


<!-- Complete JavaScript Bundle -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<!-- This Script Needs to Be added to Every Page -->
<script>
    $(document).ready(function() {

        var docHeight = $(window).height();
        var footerHeight = $('#footer').height();
        var footerTop = $('#footer').position().top + footerHeight;

        if (footerTop < docHeight)
            $('#footer').css('margin-top', 0 + (docHeight - footerTop) + 'px');
    });
</script>
</body>
</html>