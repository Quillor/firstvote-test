<?php
// if (isset($_GET['count'])) {
//   include(locate_template('/lib/count.php'));
// }
?>

<p class="text-center extra-padding">
  <button type="button" class="btn btn-primary btn-lg" id="count-votes" data-toggle="modal" data-target="#tally-modal" data-backdrop="static" data-keyboard="false">Count Votes!</button>
</p>

<div class="modal fade" id="tally-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Hang tight! We're counting votes at each precinct:</h4>
      </div>
      <div class="modal-body">
        <div id="script-progress"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btn-close" style="display: none;" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<table class="table table-responsive sortable">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Precinct</th>
      <th scope="col">School</th>
      <th scope="col">Election</th>
      <th scope="col">Created</th>
      <th scope="col">Votes</th>
      <th scope="col">Teachers</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $i = 1;
    if(is_multisite()){
        global $wpdb;
        $blogs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->blogs WHERE spam = '%d' AND deleted = '%d' and archived = '%d' and public='%d'", 0, 0, 0, 0));
        if(!empty($blogs)){
            ?><?php
            foreach($blogs as $blog){
                switch_to_blog($blog->blog_id);
                $details = get_blog_details($blog->blog_id);
                $q = new WP_Query([
                  'posts_per_page' => -1,
                  'post_type' => 'election'
                ]);
                if($q->have_posts()){
                    while($q->have_posts()){
                        $q->the_post();
                        ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><a href="<?php echo get_site_url(); ?>" target="_blank"><?php echo $details->path; ?></a></td>
                          <td><?php echo $details->blogname; ?></td>
                          <td><a href="<?php echo get_the_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a></td>
                          <td>
                            <?php
                            $the_time = new DateTime();
                            $the_time->setTimestamp(get_the_time('U'));
                            $the_time->setTimeZone(new DateTimeZone('America/New_York'));
                            echo $the_time->format('m/d/Y') . '<br />' . $the_time->format('g:ia T');
                            ?>
                          </td>
                          <td>
                            <?php
                            $n = new WP_Query([
                              'post_type' => 'ballot',
                              'posts_per_page' => -1
                            ]);
                            echo $n->found_posts;
                            ?>
                          </td>
                          <td>
                            <?php
                            $u = get_users([$blog->blog_id]);
                            foreach ($u as $user) {
                              echo $user->user_email . '<br />';
                            }
                            ?>
                          </td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                wp_reset_query();
                restore_current_blog();
            }
        }
    }
    ?>
  </tbody>
</table>
