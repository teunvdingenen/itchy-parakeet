<nav>
    <ul class="pagination">
        <li>
            <a href=<?php echo "?p=".($page-1) ?> aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php 
            for($i = 0; $i < $pages; $i++ ) {
                printf("<li><a href='?p=%s''>%s</a></li>",$i,$i+1);
            }
        ?>
        <li>
            <a href=<?php echo "?p=".($page+1) ?> aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>