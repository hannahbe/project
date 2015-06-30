<?php
/*
Template Name: About Page
*/
?>

<?php get_header();
$article = get_main_article();
$workers = get_all_workers();
?>


<div id="about-main">

    <div id="about-us">
        <div id="about-article">
            <h2><?php echo $article->title; ?></h2>
            <p><?php echo $article->content; ?></p>
        </div>
        <div id="about-us-img"><img src="<?php echo $article->image; ?>" alt="copy color"></div>
        <div style="clear: both"></div>
    </div>

    <div id="about-workers">

    <?php
        if ($workers != NULL && !empty($workers)) {
            $i = 0;
            echo '<table id="workers-table">';
            foreach ($workers as $worker) {
                if ($i % 2 == 0) {
                    $photo = $worker->image;
                    $name = $worker->title;
                    $job = $worker->content;
                }
                else {
                    echo '<tr>';
                        echo '<td><img src="' . $photo . '" alt="photo" class="worker-photo"/></td>';
                        echo '<td><img src="' . $worker->image . '" alt="photo" class="worker-photo"/></td>';
                    echo '</tr>';
                    echo '<tr>';
                        echo '<td>' . $name . '</td>';
                        echo '<td>' . $worker->title . '</td>';
                    echo '</tr>';
                    echo '<tr>';
                        echo '<td class="worker-job">' . $job . '</td>';
                        echo '<td class="worker-job">' . $worker->content . '</td>'; 
                    echo '</tr>';
                }  
                $i++;
            }
            if ($i % 2 == 1) {
                echo '<tr><td><img src="' . $photo . '" alt="photo" class="worker-photo"/></td><td></td></tr>';
                echo '<tr><td>' . $name . '</td><td></td</tr>';
                echo '<tr><td class="worker-job">' . $job . '</td><td></td></tr>';
            }
            echo '</table>';
        }
    ?>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        var img_width = 0.12 * window.innerWidth;
        var images = document.getElementsByClassName("worker-photo");
        for (var i = 0; i < images.length; i++)
            images[i].style.width = (img_width + "px");
    });
</script>

<?php get_footer(); ?>