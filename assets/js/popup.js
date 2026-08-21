$(function(){
    var popUps = $('.pop-up');
    var currentIndex = 0;
    var stopAutohide;
    
    function showWindow() {
        // Hide all pop-ups and remove d-flex class
        popUps.hide().removeClass('d-flex').addClass('d-none');
        // Show current pop-up and add d-flex class
        $(popUps[currentIndex]).show().addClass('d-flex').removeClass('d-none');
        $('#pop-main').css('overflow', 'hidden');
        // Set timeout to hide the window after 5 seconds
        stopAutohide = setTimeout(hideWindow, 5000);
    }
    function hideWindow() {
        // Hide current pop-up and remove d-flex class
        $(popUps[currentIndex]).hide().removeClass('d-flex').addClass('d-none');
        $('#pop-main').css('overflow', 'visible');
        // Update current index to the next pop-up
        currentIndex = (currentIndex + 1) % popUps.length;
        // Set timeout to show the next window after 5 seconds
        stopAutohide = setTimeout(showWindow, 5000);
    }
    // Auto open after 2 seconds initially
    setTimeout(showWindow, 2000);
    // Close button click event
    $('.close-btn').click(function(){
        clearTimeout(stopAutohide);
        hideWindow();
        setTimeout(showWindow, 5000);
    });
});