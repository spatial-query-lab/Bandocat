<?php
    session_start();
    include 'config.php';

    if(isset($_POST['submit']))
    {
        $library = "";
        $docTitle = "";

 
        $allowedExts = array("gif", "jpeg", "jpg", "png", "tif");

        if (!empty($_FILES["file1"]["name"])) 
        {
                        
            $fileName1 = $_FILES["file1"]["name"];

            $ext1 = pathinfo($fileName1, PATHINFO_EXTENSION);

            if(in_array($ext1, $allowedExts))
            {
                if( $_FILES["file1"]["error"] > 0) 
                {
                    ?>
                    <div class = "error message"><p><?php
                        echo "Error: " . $_FILES["file1"]["error"] . "<br>";
                        echo "The uploaded file" . $_FILES["file1"]["error"] . "is not an image. Please upload a valid file!"; 

                    ?></p></div>
                    <br><a href = "uploadMap.php">Back</a>
                    <?php
                }
                else
                {    

                    $exp = explode ("-", $fileName1);
                    $name = $exp[0];

                    if (file_exists("C:/uploads/" . $name . "/" . $fileName1))
                    {
                        ?>
                        <div class = "error message"><p><?php
                            echo $fileName1 . " already exists. ". "<br>";
                        ?></p></div>
                        <br><a href = "uploadMap.php">Back</a>
                    <?php
                    }               
                    else
                    {
                        $path = "C:/uploads/" . $name . "/";
                        if(is_dir($path))                       //check if directory uploads/$name exists
                        {
                            

                            $img1Path = $path . $fileName1;
                            $infoFile1 = pathinfo($fileName1);
                            $nameFile1NoExt = basename($fileName1, '.'.$infoFile1['extension']);
   
                            $pathThumbNail = "C:/uploads/thumbnails/";
                            
                            if(is_dir($pathThumbNail))          //check if directory thumbnails exists
                            {
                                                            

                                $fileName = $fileName1;

                                $sql = "INSERT INTO mapinformation (library_index,document_title,file_name) 
                                    VALUES ('$library','$docTitle','$img1Path')";


                                $r = mysql_query($sql);

                                if ($r)
                                {
                                    move_uploaded_file($_FILES["file1"]["tmp_name"], $path . $fileName1);
                                    
                                    $exec1 = "convert " . $img1Path . " -deskew 40% -fuzz 50% -trim +repage -resize 200 " . $pathThumbNail . $nameFile1NoExt . ".jpg";                          
                                    exec($exec1, $yaks1);
                            
                                    ?>
                                    <div class = "success message"><p>Map information input successfully!!!</p></div>
                                    <?php
                                        echo "Upload: " . $fileName1 . "<br>";
                                        echo "Type: " . $_FILES["file1"]["type"] . "<br>";
                                        echo "Size: " . ($_FILES["file1"]["size"] / 1024 / 1024) . " MB<br><br>";                    
                                    ?>

                                    <br><a href = "menu.php">Back</a>
                                    <?php
                                }
                                else
                                {
                                ?>                          
                                    <div class = "error message"><p>Can't upload map information!</p></div>
                                    <br><a href = "uploadMap.php">Back</a>
                                <?php
                                }
                            }
                            else
                            {
                            ?>
                                <div class = "error message"><p><?php echo "There is no folder " . $pathThumbNail . "\r\n"; ?></p></div>                
                            
                                <a href = "uploadMap.php">Back</a>
                            <?php
                            }
                        }
                        else            //if directory uploads/$name doesn't exist
                        {
                            if(mkdir($path, 0777))
                            {                   
                                

                                $img1Path = $path . $fileName1;


                                $infoFile1 = pathinfo($fileName1);
                                $nameFile1NoExt = basename($fileName1, '.'.$infoFile1['extension']);

                                
                                $pathThumbNail = "C:/uploads/thumbnails/";
                

                                if(is_dir($pathThumbNail))          //check if directory thumbnails exists
                                {
                                                                        

                                    $fileName = $fileName1;


                                    $sql = "INSERT INTO mapinformation (library_index,document_title,file_name) 
                                    VALUES ('$library','$docTitle','$img1Path')";


                                    $r = mysql_query($sql);

                                    if ($r)
                                    {
                                        move_uploaded_file($_FILES["file1"]["tmp_name"], $path . $fileName1);
 

                                        $exec1 = "convert " . $img1Path . " -deskew 40% -fuzz 50% -trim +repage -resize 200 " . $pathThumbNail . $nameFile1NoExt . ".jpg";                          
                                        exec($exec1, $yaks1);

                                        ?>
                                        <div class = "success message"><p>Map information input successfully!!!</p></div>
                                        <?php
                                            echo "Upload: " . $fileName1 . "<br>";
                                            echo "Type: " . $_FILES["file1"]["type"] . "<br>";
                                            echo "Size: " . ($_FILES["file1"]["size"] / 1024 / 1024) . " MB<br><br>";                  
 
                                        ?>
                                        <br><a href = "menu.php">Back</a>
                                        <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <div class = "error message"> <p>Can't input map information!</p> </div>
                                        
                                        <br><a href = "uploadMap.php">Back</a>
                                        <?php
                                    }
                                }
                                else
                                {
                                ?>
                                    <div class = "error message">
                                        <p><?php
                                            echo "There is no folder " . $pathThumbNail . "\r\n";
                                        ?></p>
                                    </div>

                                    <br><a href = "uploadMap.php">Back</a>
                                <?php
                                }
                            }
                            else
                            {
                            ?>
                                <div class = "error message">
                                    <p><?php
                                        echo "Failed to create the folder " . $path . "\r\n";
                                    ?></p>
                                </div>

                                <br><a href = "uploadMap.php">Back</a>
                            <?php
                            }
                        }
                    }
                }
            }
            else
            {
            ?>
                <div class = "error message"> <p>Invalid input files!</p> </div>     
                    
                <br><a href = "uploadMap.php">Back</a>
            <?php
            }
        }
        else if(!empty($_FILES["file1"]["name"]))        //only file1
        {
            $fileName1 = $_FILES["file1"]["name"];
            
            $ext1 = pathinfo($fileName1, PATHINFO_EXTENSION);
            
            if(in_array($ext1, $allowedExts) )
            {
                if( $_FILES["file1"]["error"] > 0 )
                {
                ?>
                    <div class = "error message">
                        <p><?php
                            echo "Error: " . $_FILES["file1"]["error"] . "<br>";
                            echo "The uploaded file" . $_FILES["file1"]["error"] . "is not an image. Please upload a valid file!"; 
                        ?></p>
                    </div>

                    <br><a href = "uploadMap.php">Back</a>
                <?php   
                }               
                else
                {                               
                    $exp = explode ("-", $fileName1);
                    $name = $exp[0];

                    if (file_exists("C:/uploads/" . $name . "/" . $fileName1))
                    {
                    ?>
                        <div class = "error message">
                            <p><?php echo $fileName1 . " already exists. "; ?></p>
                        </div>

                        <br><a href = "uploadMap.php">Back</a>
                    <?php
                    }
                    else
                    {   
                        $path = "C:/uploads/" . $name . "/";
                        if(is_dir($path))                           //check if directory uploads/$name exists
                        {
                            
                            
                            $img1Path = $path . $fileName1;

                            $infoFile1 = pathinfo($fileName1);
                            $nameFile1NoExt = basename($fileName1, '.'.$infoFile1['extension']);
                            
                            $pathThumbNail = "C:/uploads/thumbnails/";
                            
                            if(is_dir($pathThumbNail))          //check if directory thumbnails exists
                            {               

                                $fileName = $fileName1;
                                $fileNameBack = "";

                                $sql = "INSERT INTO mapinformation (library_index,document_title,file_name) 
                                    VALUES ('$library','$docTitle','$img1Path')";
                                $r = mysql_query($sql);

                                if ($r)
                                {
                                    move_uploaded_file($_FILES["file1"]["tmp_name"], $path . $fileName1);

                                    $exec = "convert " . $img1Path . " -deskew 40% -fuzz 50% -trim +repage -resize 200 " . $pathThumbNail . $nameFile1NoExt . ".jpg";                           
                                    exec($exec, $yaks);

                                    ?>

                                    <div class="success message">
                                        <p>Map information input successfully!!!</p>                                             
                                    </div>

                                    <?php
                                        echo "<br>Upload: " . $fileName1 . "<br>";
                                        echo "Type: " . $_FILES["file1"]["type"] . "<br>";
                                        echo "Size: " . ($_FILES["file1"]["size"] / 1024 / 1024) . " MB<br>";

                                    ?>

                                    <br><a href = "menu.php">Back</a>
                                    <?php
                                }
                                else
                                {
                                                
                                    ?>
                                    <div class="error message">
                                        <p>Can't input map information!</p>                                          
                                    </div>

                                    <br><a href = "uploadMap.php">Back</a>
                                    <?php
                                }
                            }                          
                            else
                            {
                                
                            ?>
                                <div class="info message">
                                    <p><?php echo "There is no folder " . $pathThumbNail . "\r\n"; ?></p>                                            
                                </div>
                                <br><a href = "uploadMap.php">Back</a>
                            <?php
                            }                   
                                        
                        }
                        else            //if directory uploads/$name doesn't exist
                        {
                            if(mkdir($path, 0777))      
                            {   
                                
                                
                                $img1Path = $path . $fileName1;

                                $infoFile1 = pathinfo($fileName1);
                                $nameFile1NoExt = basename($fileName1, '.'.$infoFile1['extension']);

                                $pathThumbNail = "C:/uploads/thumbnails/";

                                if(is_dir($pathThumbNail))          //check if directory thumbnails exists
                                {                           

                                    $fileName = $fileName1;


                                $sql = "INSERT INTO mapinformation (library_index,document_title,file_name) 
                                    VALUES ('$library','$docTitle','$img1Path')";
                                    $r = mysql_query($sql);

                                    if ($r)
                                    {
                                        move_uploaded_file($_FILES["file1"]["tmp_name"], $path . $fileName1);
                                        $exec = "convert " . $img1Path . " -deskew 40% -fuzz 50% -trim +repage -resize 200 " . $pathThumbNail . $nameFile1NoExt . ".jpg";                           
                                        exec($exec, $yaks);

                                        ?>

                                        <div class="success message">
                                            <p>Map information input successfully!!!</p>                                             
                                        </div>

                                        <?php
                                            echo "<br>Upload: " . $fileName1 . "<br>";
                                            echo "Type: " . $_FILES["file1"]["type"] . "<br>";
                                            echo "Size: " . ($_FILES["file1"]["size"] / 1024 / 1024) . " MB<br>";

                                        ?>

                                        <br><a href = "menu.php">Back</a>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="error message">
                                            <p>Can't input map information!</p>                                          
                                        </div>

                                        <br><a href = "uploadMap.php">Back</a>
                                        <?php
                                    }
                                }                          
                                else
                                {
                                ?>
                                    <div class="info message">
                                        <p><?php echo "There is no folder " . $pathThumbNail . "\r\n"; ?></p>                                            
                                    </div>
                                    <br><a href = "uploadMap.php">Back</a>
                                <?php
                                }        
                            }
                            else
                            {
                                ?>
                                    <div class="error message">
                                        <p><?php echo "Failed to create the folder " . $path . "\r\n";?></p>                                             
                                    </div>

                                    <br><a href = "uploadMap.php">Back</a>
                                <?php
                            }
                        }   
                    }
                }
            }
            else
            {
                ?>
                    <div class="error message">
                        <p>Invalid input files!</p>                                          
                    </div>

                    <br><a href = "uploadMap.php">Back</a>
                <?php
            }
        }   
    }    
    else
    {
    ?>
        <div class="error message">
            <p>Can't isset submit</p>                                            
        </div>

        <br><a href = "uploadMap.php">Back</a>
    <?php
    }


?>

<head>
<link rel = "stylesheet" type = "text/css" href = "styles.css" />
<script type="text/javascript">
    var myMessages = ['info','warning','error','success'];


    function showMessage(type)
    {
        $('.'+ type +'-trigger').click(function(){
                              
              $('.'+type).animate({margin-top:"0"}, 500);
        });
    }

    $(document).ready(function(){
         
                 
         // Show message
         for(var i=0;i<myMessages.length;i++)
         {
            showMessage(myMessages[i]);
         }
         
         
         
    }); 

</script>
</head>