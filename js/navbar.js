/**
 * Created by Petr on 19. 4. 2015.
 */
jQuery(document).ready(function() {
    jQuery('.hovered a').hover(
        function() {
            console.log("Hover in");
            //jQuery('.hovered ul').addClass("toggled");
        }, function() {
            //jQuery('.hovered ul').removeClass("toggled");
    });
});