<?php
    include "../connect/connect.php";
    include "../connect/session.php";

    
    if(isset($_SESSION['memberId'])){
        $memberId = $_SESSION['memberId'];
    } else {
        $memberId = 0;
    }

    
    if(isset($_GET['introId'])){
        $introId = $_GET['introId'];
    } else {
        Header("Location: introduce.php");
    }


    $isUserLoggedIn = isset($_SESSION["memberId"]);

    // $introSql = "SELECT * FROM Intro WHERE introId = '$introId'";
    // $introResult = $connect -> query($introSql);
    // $introInfo = $introResult -> fetch_array(MYSQLI_ASSOC);

    // IntroComment 테이블의 introId와 $introId 변수 비교
    $commentSql = "SELECT * FROM IntroComment WHERE introId = '$introId' AND commentDelete = '1' ORDER BY commentId DESC";
    $commentResult = $connect->query($commentSql);
    $commentInfo = $commentResult->fetch_array(MYSQLI_ASSOC);

    $commentCountSql = "SELECT COUNT(*) AS commentCount FROM IntroComment WHERE introId = '$introId' AND commentDelete = '1'";
    $commentCountResult = $connect->query($commentCountSql);
    $commentCountInfo = $commentCountResult->fetch_array(MYSQLI_ASSOC);
    $commentCount = $commentCountInfo['commentCount'];

    $updateCommentCountSql = "UPDATE Intro SET introComment = $commentCount WHERE introId = '$introId'";
    $connect->query($updateCommentCountSql);

    $commentName = isset($_SESSION['youId']) ? $_SESSION['youId'] : '';

    //초기 좋아요 및 싫어요 수 설정
    $initialLikes = 0;
    $initialDislikes = 0;

    // 조회수 업데이트
    $updateViewCountSql = "UPDATE Intro SET introView = introView + 1 WHERE introId = '$introId'";
    $connect->query($updateViewCountSql);

    // 조회수 가져오기
    $getViewCountSql = "SELECT introView FROM Intro WHERE introId = '$introId'";
    $viewCountResult = $connect->query($getViewCountSql);
    $viewCountInfo = $viewCountResult->fetch_array(MYSQLI_ASSOC);

    $viewcount = $viewCountInfo['introView'];
    
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Go!교복</title>
    
    <link rel="stylesheet" href="../assets/css/introD.css">
    <link rel="stylesheet" href="../assets/css/introComment.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- CSS -->
    <?php include "../include/head.php" ?>

</head>
<body>
    <?php include "../include/skip.php" ?>
    <!-- //skip -->

    <?php include "../include/header.php" ?>
    
    <main id="main" role="main">
        <div class="intro_detail_page">
            <div class="container detail_page">
                <div class="cont__top">
                    <div class="cont__top__profile">
                        <img src="../assets/img/profile.png" alt="">
                    </div>
                    <div class="cont__top__desc" id="schoolInfo">

                    </div>
                </div>
                <div class="cont__mid">

                    <div class="swiper mySwiper image-slider">
                        <div class="swiper-wrapper" id="contentsInner"></div>
                        <div class="swiper-pagination"></div>
                    </div>

                </div>
                <div class="cont__bottom">
                    <div class="cont__bottom1">
                         <div class="goodAndbad">
                            <div class="cont__bottom__like">
                            <img id="goodImage" class="like-button" src="../assets/img/good.svg" alt="good" data-introid="<?php echo $introId; ?>">
                                <p id="likeCount">0</p>
                            </div>
                            <div class="cont__bottom__dislike">
                                <img id="badImage" class="dislike-button" src="../assets/img/hate.svg" alt="bad" data-introid="<?php echo $introId; ?>">
                                <p id="dislikeCount">0</p>
                            </div>    
                        </div>
                        <div class="cont__bottom__comment">
                            <img src="../assets/img/chat.svg" alt="comment">
                        </div>
                    </div>
                    <div class="cont__bottom__view">
                        <?php
                            echo number_format($viewcount) . " views";
                        ?>
                    </div>
                    <div class="cont__bottom__desc" id="schoolInfoSub">
                        
                    </div>
                </div>
            </div>

            <div class="container comment_page">
                <div class="cont__top">COMMENT</div>

                <div class="comment_inner">
                <?php
if ($commentResult->num_rows == 0) { ?>
    <div class="cont__comment__list" id="commentList">
        <div class="comment">
            <div class="comment__top">
                <div class="comment__id">댓글이 없습니다.</div>
            </div>
            <div class="comment__cont">😥 댓글을 작성해주세요ㅠ</div>
        </div>
    </div>
<?php } else {
    function maskName($name)
    {
        if (strlen($name) <= 2) {
            return $name; // 이름이 두 글자 이하일 경우, 마스킹하지 않음
        }

        $start = 2; // 마스킹 시작 위치 (두 번째 글자)
        $length = strlen($name) - 4; // 마스킹할 글자 수 (중간 글자 제외)
        $maskedPart = str_repeat('*', $length); // 글자 수만큼 '*'로 채움
        $maskedName = substr_replace($name, $maskedPart, $start, $length); // 이름을 마스킹된 문자열로 교체

        return $maskedName;
    }

    foreach ($commentResult as $comment) {
        $commentName = $comment['commentName']; // 댓글 작성자의 이름
        $maskedName = maskName($commentName, 2); // 중간 2 글자만 표시
        // 이제 $maskedName을 사용할 수 있습니다.

        ?>
        <div class="cont__comment__list" id="commentList">
            <div class="profile"><div class="avata"></div></div>
            <div class="comment">
                <div class="comment__top">
                    <div class="comment__id"><?= $maskedName ?></div>
                    <div class="comment__date"><?= date('Y-m-d H:i', $comment['regTime']) ?></div>
                </div>
                <div class="comment__cont"><?= $comment['commentMsg'] ?></div>
                <div class="comment__likecount">좋아요 <span class="like-count" data-commentid="<?= $comment['commentId'] ?>"><?= $comment['commentLike'] ?></span>개</div>
            </div>
            <button class="heart commentLikeBtn" id="heart" data-commentid="<?= $comment['commentId'] ?>" data-memberid="<?= $comment['memberId'] ?>">
                <img src="../assets/img/heart.svg" alt="heart">
            </button>
        </div>
    <?php }
}
?>




        
                    <!-- <div class="cont__comment__list" id="commentList">
                        <div class="profile"><img src="../assets/img/profile.png" alt="profile"></div>
                        <div class="comment">
                            <div class="comment__top">
                                <div class="comment__id">wlqwhaqhsownffo</div>
                                <div class="comment__date">1일</div>
                            </div>
                            <div class="comment__cont">생각만 해도 재밋을거 같음</div>
                            <div class="comment__likecount">좋아요 0개</div>
                        </div>
                        <div class="heart" id="heart"><img src="../assets/img/heart.svg" alt="heart"></div>
                    </div> -->
                
                </div>

                <div class="cont__comment">                      
                    <div class="emotion">
                        <p class="cont__comment__emotion" data-emoji="❤️">❤️</p>
                        <p class="cont__comment__emotion" data-emoji="🙌">🙌</p>
                        <p class="cont__comment__emotion" data-emoji="🔥">🔥</p>
                        <p class="cont__comment__emotion" data-emoji="👏">👏</p>
                        <p class="cont__comment__emotion" data-emoji="😥">😥</p>
                        <p class="cont__comment__emotion" data-emoji="😍">😍</p>
                        <p class="cont__comment__emotion" data-emoji="😯">😯</p>
                        <p class="cont__comment__emotion" data-emoji="😂">😂</p>
                    </div>
                    <div class="cont__comment__write">
                        <div class="profile"><img src="../assets/img/profile.png" alt=""></div>
                        <div class="write">
                        <input type="text" id="commentWrite" name="commentWrite" placeholder="<?php echo $commentName; ?>(으)로 댓글 달기...">
                            <button type="button" id="commentWriteBtn">게시</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <!-- //main -->

    <?php include "../include/footer.php" ?>
    <!-- //footer -->

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    
    
    <!-- 좋아요 클릭 이벤트 -->
    <script>
        // 댓글 프로필 랜덤 돌리기
        const avataImages = [
            "AngrywithFang.svg",
            "Awe.svg",
            "Blank.svg",
            "Calm.svg",
            "Cheek.svg",
            "ConcernedFear.svg",
            "Concerned.svg",
            "Contempt.svg",
            "Cute.svg",
            "Cyclops.svg",
            "Driven.svg",
            "EatingHappy.svg",
            "Explaining.svg",
            "EyesClosed.svg",
            "Fear.svg",
            "Hectic.svg",
            "LovingGrin1.svg",
            "LovingGrin2.svg",
            "Monster.svg",
            "Old.svg",
            "Rage.svg",
            "Serious.svg",
            "SmileBig.svg",
            "SmileLOL.svg",
            "SmileTeeth Gap.svg",
            "Smile.svg",
            "Solemn.svg",
            "Suspicious.svg",
            "Tired.svg",
            "VeryAngry.svg",
            ]
        const commentViews = document.querySelectorAll(".comment_profile");
        commentViews.forEach((view, i) => {
            const avata = view.querySelector(".avata");
            const rand = avataImages[Math.floor(Math.random() * avataImages.length)];
            
            console.log(rand);
            avata.style.backgroundImage = `url(../assets/face/${rand})`;
        });

        

        document.querySelectorAll(".commentLikeBtn").forEach(function(button) {
            const img = button.querySelector("img"); // 현재 버튼의 이미지 요소
            let liked = false; // 현재 버튼의 클릭 상태
            button.addEventListener("click", function() {
                const commentContainer = this.closest(".cont__comment__list");
                
                // 여기서 commentContainer에 active 클래스를 추가합니다
                commentContainer.classList.add("active");
                const commentId = this.getAttribute("data-commentid");
                this.disabled = true;  // Disable the button immediately

                if (!this.classList.contains("heartLiked")) {
                    this.classList.add("heartLiked");
                    // 좋아요 버튼에 liked 클래스 추가
                    img.setAttribute("src", "../assets/img/heart_on.svg"); // 새로운 이미지로 변경
                } else {
                    this.classList.remove("heartLiked");
                    // 이미 클릭된 상태이면 liked 클래스 제거
                    img.setAttribute("../assets/img/heart_on.svg"); // 원래 이미지로 변경
                }

                this.disabled = true;  // 클릭 후 버튼 비활성화

                // AJAX request
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "introCommentLike.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            const likeCountElement = document.querySelector(".like-count[data-commentid='" + commentId + "']");

                            // 업데이트된 값을 표시
                            likeCountElement.textContent = response.newLikeCount;
                        } else {
                            alert("이미 클릭한 댓글 입니다");
                            button.disabled = false;
                        }
                    }
                };
                xhr.send("commentId=" + encodeURIComponent(commentId));
            });
        });
    </script>

    <!-- 이미지 슬라이드, 댓글 쓰기 -->
    <script>
        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });




        document.addEventListener("DOMContentLoaded", function () {
            const commentWriteInput = document.getElementById("commentWrite");
            const emotionIcons = document.querySelectorAll(".cont__comment__emotion");

            emotionIcons.forEach(function (icon) {
                icon.addEventListener("click", function () {
                    const emoji = icon.getAttribute("data-emoji");
                    const currentText = commentWriteInput.value;
                    commentWriteInput.value = currentText + emoji;

                    // input 요소에 포커스를 줍니다.
                    commentWriteInput.focus();
                });
            });

            $("#commentWriteBtn").click(function () {
                if ($("#commentWrite").val() == "") {
                    alert("댓글을 작성해주세요!");
                    $("#commentWrite").focus();
                } else {
                    if (!<?= $isUserLoggedIn ? 'true' : 'false' ?>) {
                        alert("회원만 이용 가능합니다. 로그인 후 다시 시도해주세요.");
                        return;
                    }
                    // 사용자가 로그인한 경우 댓글 작성 요청을 보냄
                    $.ajax({
                        url: "introCommentWrite.php",
                        method: "POST",
                        dataType: "json",
                        data: {
                            "introId": "<?=$introId?>",
                            "memberId": <?=$memberId?>,
                            "name": "<?=$commentName?>",
                            "msg": $("#commentWrite").val(),
                        },
                        success: function (data) {
                            console.log(data);
                            if (data.introId) {
                                console.log("introId: " + data.introId);
                            } else {
                                console.log("introId가 없습니다.");
                            }
                            location.reload();
                        },
                        error: function (request, status, error) {
                            console.log("request", request);
                            console.log("status", status);
                            console.log("error", error);
                        }
                    });
                }
            });
            $("#commentWrite").keypress(function (event) {
                if (event.which === 13) {
                    event.preventDefault();
                    $("#commentWriteBtn").click();
                }
            });
        });
        
    </script>

    <!-- json 교복 정보 가져오기 -->
    <script>
        //선택자
        const regionElement = document.querySelector('.region');
        const NameElement = document.querySelector('.name');
        const Uniformtypes = document.querySelectorAll('.uniformtypes');
        const contents = document.querySelector('#contentsInner');
        const url = window.location.href;
        const queryString = decodeURIComponent(url.split('?')[1]);


        // 정보 가져오기
        const fetchgGobok = () => {
            fetch("https://raw.githubusercontent.com/jinhomun/webs2024/main/blog_phpJSON/gobok.json")
                .then(res => res.json())
                .then(items => {
                    gobokInfo = items.map((item, idex) => {
                        const formattedGobok = {
                            infoRegion: item.region,
                            infoName: item.school,
                            infoUniformtypes: item.uniform_types,
                            infoUniformimg: item.uniform_img
                        }
                        return formattedGobok;
                    });
                    console.log(gobokInfo);
                    updataGobok();
                })
        }

        


        // 정보 출력
        const updataGobok = () => {
            const gobokArray = [];
            const url = window.location.href; // 현재 페이지의 URL을 가져옵니다.
            const queryString = decodeURIComponent(url.split('?')[1]);

            // queryString에서 introId를 추출합니다.
            const introId = queryString.split('=')[1];

            const schoolInfo = gobokInfo.find(item => item.infoName === introId);

            if (schoolInfo) {
                // 해당 학교 정보에 대한 이미지 슬라이드 반복
                schoolInfo.infoUniformimg.forEach(imageUrl => {
                    gobokArray.push(`
                        <div class="swiper-slide">${imageUrl}</div>
                    `);
                });
            } else {
                console.log("해당 학교 정보를 찾을 수 없습니다.");
            }

            contents.innerHTML = gobokArray.join("");

            // 정보를 출력한 이후, 학교 정보를 표시하는 함수를 호출
            updateGobokWithSchoolInfo(gobokInfo);
        }
        // 학교 정보를 출력하는 함수
        const updateGobokWithSchoolInfo = (gobokInfo) => {
            const gobokArray = [];
            const url = window.location.href; // 현재 페이지의 URL을 가져옵니다.
            const queryString = decodeURIComponent(url.split('?')[1]);

            // queryString에서 introId를 추출합니다.
            const introId = queryString.split('=')[1];

            const schoolInfo = gobokInfo.find(item => item.infoName === introId);
            

            if (schoolInfo) {
                const schoolInfoElement = document.getElementById('schoolInfo');
                const schoolInfoSubElement = document.getElementById('schoolInfoSub');
                schoolInfoElement.innerHTML = `${schoolInfo.infoName} <p>${schoolInfo.infoRegion}</p>`;
                schoolInfoSubElement.innerHTML = `<em>${schoolInfo.infoName}</em><p>${schoolInfo.infoUniformtypes.join(' ')}</p>`;
            } else {
                console.log("해당 학교 정보를 찾을 수 없습니다.");
            }
        }

        console.log(schoolInfo)

        // 페이지가 로드된 후 실행
        document.addEventListener("DOMContentLoaded", () => {
            fetchgGobok();
        });


    </script>

    <script>
        
        
        // 좋아요 수 초기화
        function initializeLikeCount() {
            var introId = '<?php echo $introId; ?>';
            var likeCountElement = $('#likeCount'); 
            var dislikeCountElement = $('#dislikeCount'); 

            // 서버에서 좋아요 수를 가져오는 AJAX 요청
            $.ajax({
                url: 'getLikeCount.php',
                type: 'GET',
                data: { 'introId': introId },
                dataType: 'json',
                success: function(response) {
                    likeCountElement.text(response.likeCount);
                    dislikeCountElement.text(response.dislikeCount);
                },
                error: function(xhr, status, error) {
                    console.log('오류가 발생했습니다: ' + error);
                }
            });
        }

        // 페이지 로딩 시 초기화 함수 호출
        $(document).ready(function() {
            initializeLikeCount();
        });




// 좋아요 버튼 클릭 이벤트 처리
$('.like-button').click(function() {
    var introId = $(this).data('introid');
    var likeCountElement = $('#likeCount');
    var dislikeCountElement = $('#dislikeCount'); // 추가: 싫어요 수 업데이트를 위한 요소
    const whiteHeader = document.querySelector("header");
    const newGoodImageSrc = '../assets/img/good_on.svg';
    $.ajax({
        url: 'introLike.php', // 서버의 PHP 스크립트
        type: 'POST', // 데이터 전송 방식
        data: { 'introId': introId, 'likeType': 'like' }, // 서버에 보낼 데이터, 'likeType' 추가
        dataType: 'json', // 응답 데이터 타입
        success: function(response) {
            if(response.error) {
                alert(response.error);
            } else {
                if(response.alreadyLiked) {
                    alert("이미 좋아요를 누르셨습니다.");
                } else {
                    likeCountElement.text(response.likeCount);
                    whiteHeader.classList.add('whiteHeader');
                    goodImage.src = newGoodImageSrc; // 이미지 변경
                    document.body.classList.add('liked');
                }
            }
        },
        error: function(xhr, status, error) {
            alert('오류가 발생했습니다: ' + error);
        }
    });
});

// 싫어요 버튼 클릭 이벤트 처리
$('.dislike-button').click(function() {
    var introId = $(this).data('introid');
    var likeCountElement = $('#likeCount');
    var dislikeCountElement = $('#dislikeCount');
    const whiteHeader = document.querySelector("header");
    const newHateImageSrc = '../assets/img/hate_on.svg';
    $.ajax({
        url: 'introLike.php', // 서버의 PHP 스크립트
        type: 'POST', // 데이터 전송 방식
        data: { 'introId': introId, 'likeType': 'dislike' }, // 서버에 보낼 데이터, 'likeType' 추가
        dataType: 'json', // 응답 데이터 타입
        success: function(response) {
            if(response.error) {
                alert(response.error);
            } else {
                if(response.alreadyLiked) {
                    alert("이미 싫어요를 누르셨습니다.");
                } else {
                    dislikeCountElement.text(response.dislikeCount); // 싫어요 수 업데이트
                    whiteHeader.classList.add('whiteHeader');
                    badImage.src = newHateImageSrc; // 이미지 변경
                    document.body.classList.add('liked');
                }
            }
        },
        error: function(xhr, status, error) {
            alert('오류가 발생했습니다: ' + error);
        }
    });
});
    </script>
</body>
</html>