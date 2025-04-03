// JS Function for hamburger menu
$(function() {
    $(".toggle").on("click", function() {
        var $menu = $(".menu");
        if ($menu.hasClass("active")) {
            $menu.removeClass("active");
            $(this).find("ion-icon").attr("name", "menu-outline");
        } else {
            $menu.addClass("active");
            $(this).find("ion-icon").attr("name", "close-outline");
        }
    });
});
//To naviage in login

function redirectToSignup() {
    window.location.href = 'login.php';
}