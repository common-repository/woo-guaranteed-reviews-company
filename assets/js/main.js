/*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    Guaranteed Reviews Company <contact@societe-des-avis-garantis.fr>
*  @copyright 2013-2017 Guaranteed Reviews Company
*  @license   LICENSE.txt
*/


function showReviews() {
    jQuery('html, body').animate({scrollTop:jQuery( "#ag-s").offset().top}, 'slow');
} 
 
function showMoreReviews(productId, reviewsNb, postsPage , pageNb, modulesDir, langId) {

    if (Math.ceil(reviewsNb / postsPage) == parseInt(pageNb)) {
        jQuery('#more-reviews').hide();
    } 

    jQuery.ajax({
        url: modulesDir,
        type: 'POST',
        data: {currentPage : pageNb, id_lang : langId, posts_per_page : postsPage, id_product : productId, nbOfReviews : reviewsNb},
        beforeSend: function() {
            jQuery('#chargement').show();
            save = jQuery(".reviewList").html();
        },
        success: function( html) {
            jQuery('#chargement').hide();
            jQuery(".reviewList").append(html);
            pageNb = pageNb + 1;

            jQuery("#more-reviews").each(function() {
	            this.attributes.onclick.nodeValue =  "return showMoreReviews(" + productId + ", " + reviewsNb + ", " + postsPage + ", " + pageNb + ", '" + modulesDir + "', '" + langId + "');";
	        });
        }
    });

    return false;
}