<div style="text-align: <?php echo $iconalign ?>; width: 100%;">
    <div id="<?php echo $icondiv ?>">

        <a href="<?php echo $link ?>"
           <?php if ($mouseovers || is_siteadmin($USER)) { ?>class="mod_connectquiz_tooltip"<?php } ?>
           style="display: inline-block;" target="<?php echo $linktarget;?>">
            <img src="<?php echo $iconurl ?>" border="0"/>
            <?php echo $clock ?>
        </a>

        <div class="mod_connectquiz_popup" style="display: none;">
            <?php echo $overtext ?>
        </div>
    </div>

    <?php echo $aftertext ?>
</div>
