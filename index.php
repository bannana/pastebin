<?php
if (empty($_POST) && empty($_GET)) {
    echo str_replace("%text%", "", file_get_contents("./default.page"));
} else if (!empty($_GET)) {
    if (isset($_GET['id'])) {
        $con = mysqli_connect("localhost","nanner","6174sql","pastebin");
        $paste = mysqli_query($con,"SELECT paste, title FROM pastes WHERE id= ".mysql_real_escape_string($_GET['id']));
        $pasteContent = mysqli_fetch_array($paste);
        $content = "<div class='text'>\n    <ol>\n";
        $count = 0;
        // Go through each line and print new List element
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $pasteContent['paste']) as $line) {
            $line = str_replace("&","&amp;", $line);
            $line = str_replace(" ","&nbsp;",$line);
            $line = str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;",$line);
            $line = str_replace("<","&#60;", $line);
            $line = str_replace(">","&#62;", $line);
            $content = $content . "<li><span class='textcontent'> $line </span></li>\n";
            $count = $count + 1;
        }
        $content = $content . "    </ol>\n</div>\n";
        mysqli_close($con);
        // Find out how wide the linenumber sidebar should be
        $lineNumberWidth = (strlen("$count")*10)+12;
        // replace some page variables and echo the result
        echo str_replace("%pasteNum%",$_GET['id'],
             str_replace("%title%",$pasteContent['title'], 
             str_replace("%lineNumberWidth%",$lineNumberWidth,
             str_replace("%content%","$content",file_get_contents("paste.page")))));
    } else if (isset($_GET['new'])) {
        $con = mysqli_connect("localhost","nanner","6174sql","pastebin");
        $paste = mysqli_query($con,"SELECT paste FROM pastes WHERE id= ".mysql_real_escape_string($_GET['new']));
        $pasteContent = mysqli_fetch_array($paste);
        $content = "";
        // Go through each line and print new List element
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $pasteContent['paste']) as $line) {
            $line = str_replace("&","&amp;", $line);
            $line = str_replace(" ","&nbsp;",$line);
            $line = str_replace("<","&#60;", $line);
            $line = str_replace(">","&#62;", $line);
            $content = $content . "$line\n";
        }
        mysqli_close($con);
        echo str_replace("%text%", $content, file_get_contents("./default.page"));
    }
} else {
    if (isset($_POST['paste']) && $_POST['title']) {
        $con = mysqli_connect("localhost","nanner","6174sql","pastebin");
        $paste = mysql_real_escape_string($_POST['paste']);
        $title = mysql_real_escape_string($_POST['title']);
        $query = "INSERT INTO pastes ( paste, title ) VALUES ( '$paste' , '$title' );";
        if (!mysqli_query($con,$query)) {
            die('Error: ' . mysqli_error($con));
        }
        $id = mysqli_insert_id($con);
        mysqli_close($con);
        header("Location: /$id");
    } else {
        echo "What did you do!?!??!";
    }
}

?>
