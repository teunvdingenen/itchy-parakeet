<nav>
    <ul class="pagination">
        <li>
            <a href=<?php echo $url.($first ? "?" : "&")."p=".($page-1) ?> aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php 
            for($i = 0; $i < $pages; $i++ ) {
                if (!$first) {
                    printf("<li><a href='".$url."&p=%s'>%s</a></li>",$i,$i+1);
                } else {
                    printf("<li><a href='".$url."?p=%s'>%s</a></li>",$i,$i+1);
                }
            }
        ?>
        <li>
            <a href=<?php echo $url.($first ? "?" : "&")."p=".($page+1) ?> aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>