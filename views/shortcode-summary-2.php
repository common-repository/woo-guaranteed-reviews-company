<?php

/**
 * displays the shortcode content
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

global $product;

?>

<script type="text/javascript">
    window.addEventListener('load', function() {
      if (document.getElementsByTagName('h1').length == 1) {
        insertUnderH1(0);
      }else if(document.getElementsByTagName('h1').length > 1){
        insertUnderH1(0);
      }else{
        document.getElementById('agWidgetH').style.display = "block";
      }

      function insertUnderH1(position){
        let widgetSummary = document.getElementById('agWidgetH');
        let title_h1 = document.getElementsByTagName('h1')[position];

        let H1style = title_h1.currentStyle || window.getComputedStyle(title_h1);
        let title_h1Margin = H1style.marginBottom;
        widgetSummary.style.marginTop = "-" + title_h1Margin;

        title_h1.parentNode.insertBefore(widgetSummary, title_h1.nextSibling);
        widgetSummary.style.display = "block";
      }
    });
</script>

<style>
	.steavisgarantisStar svg.note g{ fill: <?php echo $this->settings->get( 'star_color' ); ?> !important;}
	.steavisgarantisStats .item .name { color:  <?php echo $this->settings->get( 'star_color' ); ?> !important;}
	.steavisgarantisStats .item .stat .note { background:  <?php echo $this->settings->get( 'star_color' ); ?> !important;}
</style>
<div id="agWidgetH" class="inline agWidget rad <?php echo $this->settings->get( 'sag_lang' ); ?>">
    <div class="inner rad">
        <a class="agBt rad4" onclick="showReviews(); return false;" href="#ag-s">
            <img src="<?php echo WC_SAG_PLUGIN_URL; ?>assets/images/sag_logo_cocarde_<?php echo $this->settings->get( 'sag_lang' ); ?>.png" class="logoAg">
            <div class="reviewGlobal">
                <div class="steavisgarantisStar">
                    <span></span>
					<div class="animate" style="width:<?php echo $ratings['average'] / 5 *100?>%;overflow:hidden;position:relative;">
						<svg class="note" version="1.0" xmlns="http://www.w3.org/2000/svg"
						 width="250.000000pt" height="68.000000pt" viewBox="0 0 250.000000 68.000000"
						 preserveAspectRatio="xMidYMid meet">
							<g fill="rgba(250,0,0,1)">
								<path d="M 16.02 28.17   L 22.31 11.82   A 0.34 0.34 0.0 0 1 22.95 11.82   L 29.11 28.05   A 0.34 0.34 0.0 0 0 29.43 28.27   L 43.29 28.24   A 0.34 0.34 0.0 0 1 43.53 28.82   L 32.81 39.62   A 0.34 0.34 0.0 0 0 32.73 39.97   L 38.17 56.11   A 0.34 0.34 0.0 0 1 37.67 56.51   L 22.84 47.29   A 0.34 0.34 0.0 0 0 22.48 47.29   L 7.31 56.49   A 0.34 0.34 0.0 0 1 6.81 56.09   L 12.27 40.15   A 0.34 0.34 0.0 0 0 12.19 39.80   L 1.50 28.79   A 0.34 0.34 0.0 0 1 1.75 28.21   L 15.69 28.39   A 0.34 0.34 0.0 0 0 16.02 28.17   Z"/>
								<path d="M 79.28 28.29   L 93.14 28.22   A 0.34 0.34 0.0 0 1 93.38 28.80   L 82.66 39.66   A 0.34 0.34 0.0 0 0 82.58 40.01   L 88.04 56.15   A 0.34 0.34 0.0 0 1 87.54 56.55   L 72.70 47.28   A 0.34 0.34 0.0 0 0 72.34 47.28   L 57.15 56.50   A 0.34 0.34 0.0 0 1 56.65 56.10   L 62.13 40.14   A 0.34 0.34 0.0 0 0 62.05 39.79   L 51.34 28.78   A 0.34 0.34 0.0 0 1 51.59 28.20   L 65.53 28.40   A 0.34 0.34 0.0 0 0 65.86 28.18   L 72.17 11.87   A 0.34 0.34 0.0 0 1 72.80 11.87   L 78.96 28.07   A 0.34 0.34 0.0 0 0 79.28 28.29   Z"/>
								<path d="M 129.12 28.28   L 142.99 28.23   A 0.34 0.34 0.0 0 1 143.23 28.81   L 132.51 39.64   A 0.34 0.34 0.0 0 0 132.43 39.99   L 137.90 56.14   A 0.34 0.34 0.0 0 1 137.39 56.54   L 122.57 47.30   A 0.34 0.34 0.0 0 0 122.21 47.30   L 107.02 56.48   A 0.34 0.34 0.0 0 1 106.52 56.08   L 111.97 40.14   A 0.34 0.34 0.0 0 0 111.89 39.79   L 101.22 28.79   A 0.34 0.34 0.0 0 1 101.47 28.21   L 115.40 28.39   A 0.34 0.34 0.0 0 0 115.73 28.17   L 122.03 11.80   A 0.34 0.34 0.0 0 1 122.66 11.80   L 128.80 28.06   A 0.34 0.34 0.0 0 0 129.12 28.28   Z"/>
								<path d="M 178.98 28.27   L 192.81 28.24   A 0.34 0.34 0.0 0 1 193.06 28.82   L 182.39 39.63   A 0.34 0.34 0.0 0 0 182.31 39.98   L 187.74 56.13   A 0.34 0.34 0.0 0 1 187.24 56.53   L 172.41 47.29   A 0.34 0.34 0.0 0 0 172.05 47.29   L 156.88 56.48   A 0.34 0.34 0.0 0 1 156.38 56.07   L 161.83 40.13   A 0.34 0.34 0.0 0 0 161.75 39.78   L 151.05 28.79   A 0.34 0.34 0.0 0 1 151.30 28.21   L 165.26 28.39   A 0.34 0.34 0.0 0 0 165.58 28.17   L 171.86 11.80   A 0.34 0.34 0.0 0 1 172.50 11.80   L 178.66 28.05   A 0.34 0.34 0.0 0 0 178.98 28.27   Z"/>
								<path d="M 220.13 27.69   L 226.23 12.00   Q 226.58 11.10 226.92 12.00   L 232.93 27.72   Q 233.14 28.26 233.72 28.26   L 247.19 28.27   Q 247.99 28.27 247.43 28.84   L 237.01 39.40   A 0.90 0.89 58.3 0 0 236.80 40.32   L 242.11 56.20   Q 242.35 56.94 241.69 56.53   L 227.01 47.45   Q 226.61 47.21 226.22 47.44   L 211.29 56.43   Q 210.52 56.89 210.81 56.04   L 216.12 40.39   Q 216.27 39.94 215.94 39.60   L 205.48 28.80   Q 204.93 28.23 205.72 28.24   L 219.11 28.37   Q 219.86 28.38 220.13 27.69   Z"/>
							</g>
						</svg>
					</div>
                </div>
                <p><?php echo sprintf( _n( '(%s review)', '(%s reviews)', $reviews_query->found_posts, 'woo-guaranteed-reviews-company' ), $reviews_query->found_posts ); ?></p>
            </div>
        </a>
    </div>
</div>
