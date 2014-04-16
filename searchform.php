<form role="search" method="get" id="searchform">
    <div>
    	<input type="text" name="s" id="s" placeholder="<?php _e('Search and hit enter', 'jeo'); ?>" value="<?php if(isset($_GET['s'])) echo $_GET['s']; ?>" />
    	<div id="advanced-search-link" class="advanced-search"><?php _e("Advanced search", "arteforadomuseu"); ?></a></div>
    </div>
</form>