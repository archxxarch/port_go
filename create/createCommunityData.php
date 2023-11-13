<?php
    include "../connect/connect.php";

    for($i=1; $i<=1; $i++){
        $regTime = time();

        $sql = "INSERT INTO Community(memberId, boardTitle, boardContents, boardView, boardLike, regTime, boardImgFile) VALUES('1', 'OO학교 교복', '여기교복 어때?', '1', '5', '$regTime', '$target_file')";
        $connect -> query($sql);
    }
?>