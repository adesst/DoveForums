<?php foreach( $members as $member): ?>
<div class="panel-body">
    <?php foreach( $member as $sub): ?>
    <div class="col-lg-2 user-pic">
    <?php echo $sub->gravatar ?>
    </div>
    <div class="col-lg-4 fit member-info">
        <p><?php echo lang('Name') ?>: <?php echo sprintf('%s %s', $sub->first_name, $sub->last_name ) ?></p>
        <p><?php echo lang('JoinDate') ?>: <?php echo $sub->created_on ?></p>
        <p><?php echo lang('Rank') ?>: <?php echo $sub->rank['rank'] ?></p>
        <p><?php echo lang('LastActive') ?>: <?php echo $sub->last_active ?></p>
        <p><?php echo lang('Rating') ?>: <?php echo $sub->rank['user_xp'] ?> xp</p>
    </div>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>
