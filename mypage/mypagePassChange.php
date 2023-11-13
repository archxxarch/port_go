<?php
    include "../connect/connect.php";
    include "../connect/session.php";


    $blogSql = "SELECT count(blogID) FROM blog";
    $blogInfo = $connect->query($blogSql);
    $blogTotalCount = $blogInfo->fetch_assoc();
    $blogTotalCount = $blogTotalCount['count(blogID)'];
    $blogSql = "SELECT * FROM blog WHERE blogDelete = 1 ORDER BY blogId DESC";
    $blogInfo = $connect->query($blogSql);

    $viewNum = 10; // 한 페이지에 보여줄 게시물 수

    // 현재 페이지 설정
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $startFrom = ($page - 1) * $viewNum; // 현재 페이지의 시작 위치

    // 게시물 조회 쿼리를 변경하여 현재 페이지에서 10개의 게시물만 가져오도록 합니다.
    $blogSql = "SELECT * FROM blog WHERE blogDelete = 1 ORDER BY blogId DESC LIMIT $startFrom, $viewNum";
    $blogInfo = $connect->query($blogSql);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go!교복</title>
    
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/mypage.css">
    <style>
        .mypage__inner {
            padding: 5rem 0;
            min-height: 90vh;
        }
    </style>
    <!-- CSS -->
    <?php include "../include/head.php" ?>

</head>
<body>
    <?php include "../include/skip.php" ?>
    <!-- //skip -->

    <?php include "../include/header.php" ?>
    
    <main id="main">
        <?php include "../mypage/mypageAside.php" ?>
        <section class="join__inner join__join mypage__inner container">
            <h2>비밀번호 변경</h2>
            <p>😎 새로운 비밀번호를 입력해주세요.</p>
            <div class="join__form join__form__cont">
                <form action="joinEnd.php" name="joinEnd" method="post" onsubmit="return joinChecks();">
                    <div class="check_input">
                        <label for="youPass" class="required">비밀번호</label>
                        <input type="password" id="youPass" name="youPass" placeholder="비밀번호를 적어주세요!" class="input__style">  
                        <p class="msg" id="youPassComment"></p>
                    </div>
                    <div class="check_input">
                        <label for="youPassC" class="required">비밀번호 확인</label>
                        <input type="password" id="youPassC" name="youPassC" placeholder="다시 한번 비밀번호를 적어주세요!" class="input__style">
                        <p class="msg" id="youPassCComment"></p>
                    </div>

                    <button type="submit" class="btn__style mt100 join_result_btn" style="color: #fff;">변경하기</button>
                </form>
            </div>
        </section>
    </main>
    <!-- //main -->

    <?php include "../include/footer.php" ?>
    <!-- //footer -->
</body>
</html>