<?php
include "../connect/connect.php";
include "../connect/session.php";

// 세션에서 멤버 정보 가져오기
$memberId = $_SESSION['memberId'];
$blogAuthor = $_SESSION['youName'];

// 게시물 정보 가져오기
$blogId = $_POST['blogId'];
$blogTitle = $_POST['blogTitle'];
$blogPass = $_POST['blogPass'];
$blogContents = nl2br($_POST['blogContents']);
$blogImgType = $_FILES['blogFile']['type'];
$blogImgSize = $_FILES['blogFile']['size'];
$blogImgName = $_FILES['blogFile']['name'];
$blogImgTmp = $_FILES['blogFile']['tmp_name'];

// 게시물 정보 초기화
$blogView = 1;
$blogLike = 0;
$blogRegTime = time();
$blogDelete = 1;

// 이미지 파일이 있는지 확인
if ($blogImgType) {
    $fileTypeExtension = explode("/", $blogImgType);
    $fileType = $fileTypeExtension[0];  // image
    $fileExtension = $fileTypeExtension[1];  // 이미지 확장자

    // 이미지 타입 확인
    if ($fileType === "image") {
        if ($fileExtension === "jpg" || $fileExtension === "jpeg" || $fileExtension === "png" || $fileExtension === "gif" || $fileExtension === "webp") {
            $blogImgDir = "../assets/blog/";
            $blogImgName = "Img_" . time() . rand(1, 99999) . "." . "{$fileExtension}";
        } else {
            echo "<script>alert('이미지 파일 형식이 아닙니다.')</script>";
        }
    } else {
        echo "<script>alert('이미지 파일이 아닙니다.')</script>";
    }
} else {
    echo "<script>alert('이미지 파일을 첨부하지 않았습니다.')</script>";
    echo "<script>window.location.href = 'cummunity.php';</script>";
}

// 이미지 사이즈 확인
if ($blogImgSize > 10000000) {
    echo "<script>alert('이미지 파일 용량이 10MB를 초과했습니다. 사이즈를 줄여주세요.')</script>";
} else {
    // 이미지 업로드
    $result = move_uploaded_file($blogImgTmp, $blogImgDir . $blogImgName);
}

// 이미지 업로드 및 비밀번호 확인 후 게시물 업데이트
if ($result) {
    // 이미지 업로드 성공, 비밀번호 확인
    $blogTitle = $connect->real_escape_string($blogTitle);
    $blogContents = $connect->real_escape_string($blogContents);

    // 비밀번호 확인 쿼리
    $sql = "SELECT * FROM myMembers WHERE memberId = $memberId";
    $result = $connect->query($sql);

    if ($result) {
        $info = $result->fetch_array(MYSQLI_ASSOC);

        if ($info['memberId'] === $memberId && $info['youPass'] === $blogPass) {
            // 비밀번호가 일치하는 경우 게시물 업데이트
            $sql = "UPDATE blog SET blogTitle = '$blogTitle', blogContents = '$blogContents', blogAuthor = '$blogAuthor', blogRegTime = '$blogRegTime', blogView = '$blogView', blogLike = '$blogLike', blogImgFile = '$blogImgName', blogImgSize = '$blogImgSize', blogDelete = '$blogDelete' WHERE blogId = $blogId";
            $connect->query($sql);
            echo "<script>alert('게시글이 성공적으로 수정되었습니다.')</script>";
            echo '<script>window.location.href = "cummunity.php";</script>';
        } else {
            echo "<script>alert('비밀번호가 틀렸습니다. 다시 한번 확인해주세요!')</script>";
            echo "<script>window.history.back()</script>";
        }
    } else {
        echo "<script>alert('관리자에게 문의하세요.')</script>";
    }
}
?>











