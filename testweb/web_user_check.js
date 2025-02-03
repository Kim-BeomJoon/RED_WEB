document.addEventListener('DOMContentLoaded', () => {
    // 로컬 저장소에 메인 페이지 방문 횟수 저장
    const visitKey = 'mainPageVisitCount';
    let visitCount = localStorage.getItem(visitKey);

    if (!visitCount) {
        visitCount = 0; // 방문 횟수 초기화
    }
    visitCount = parseInt(visitCount) + 1; // 방문 횟수 증가
    localStorage.setItem(visitKey, visitCount);

    // 콘솔에 방문 횟수 출력 (디버깅용)
    console.log(`Main page has been visited ${visitCount} times.`);
});

window.onload = function() {
    // 서버에서 관리자 여부 확인
    fetch('web_admin_check.php')  // check_admin.php 파일 호출
        .then(response => response.json())
        .then(data => {
            if (data.isAdmin) {
                // 관리자인 경우, 관리자 링크를 보여줌
                document.getElementById('admin-link').style.display = 'inline';
            }
        })
        .catch(error => console.error('관리자 권한 확인 실패:', error));
};