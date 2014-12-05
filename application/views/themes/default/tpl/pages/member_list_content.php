<div class="panel-body">
    <ul class="alphabet-nav">
        <li><a data-letter="A" href="<?php echo base_url('forums/members/view/')?>/A">A</a></li>
        <li><a data-letter="B" href="<?php echo base_url('forums/members/view/')?>/B">B</a></li>
        <li><a data-letter="C" href="<?php echo base_url('forums/members/view/')?>/C">C</a></li>
        <li><a data-letter="D" href="<?php echo base_url('forums/members/view/')?>/D">D</a></li>
        <li><a data-letter="E" href="<?php echo base_url('forums/members/view/')?>/E">E</a></li>
        <li><a data-letter="F" href="<?php echo base_url('forums/members/view/')?>/F">F</a></li>
        <li><a data-letter="G" href="<?php echo base_url('forums/members/view/')?>/G">G</a></li>
        <li><a data-letter="H" href="<?php echo base_url('forums/members/view/')?>/H">H</a></li>
        <li><a data-letter="I" href="<?php echo base_url('forums/members/view/')?>/I">I</a></li>
        <li><a data-letter="J" href="<?php echo base_url('forums/members/view/')?>/J">J</a></li>
        <li><a data-letter="K" href="<?php echo base_url('forums/members/view/')?>/K">K</a></li>
        <li><a data-letter="L" href="<?php echo base_url('forums/members/view/')?>/L">L</a></li>
        <li><a data-letter="M" href="<?php echo base_url('forums/members/view/')?>/M">M</a></li>
        <li><a data-letter="N" href="<?php echo base_url('forums/members/view/')?>/N">N</a></li>
        <li><a data-letter="O" href="<?php echo base_url('forums/members/view/')?>/O">O</a></li>
        <li><a data-letter="P" href="<?php echo base_url('forums/members/view/')?>/P">P</a></li>
        <li><a data-letter="Q" href="<?php echo base_url('forums/members/view/')?>/Q">Q</a></li>
        <li><a data-letter="R" href="<?php echo base_url('forums/members/view/')?>/R">R</a></li>
        <li><a data-letter="S" href="<?php echo base_url('forums/members/view/')?>/S">S</a></li>
        <li><a data-letter="T" href="<?php echo base_url('forums/members/view/')?>/T">T</a></li>
        <li><a data-letter="U" href="<?php echo base_url('forums/members/view/')?>/U">U</a></li>
        <li><a data-letter="V" href="<?php echo base_url('forums/members/view/')?>/V">V</a></li>
        <li><a data-letter="W" href="<?php echo base_url('forums/members/view/')?>/W">W</a></li>
        <li><a data-letter="X" href="<?php echo base_url('forums/members/view/')?>/X">X</a></li>
        <li><a data-letter="Y" href="<?php echo base_url('forums/members/view/')?>/Y">Y</a></li>
        <li><a data-letter="Z" href="<?php echo base_url('forums/members/view/')?>/Z">Z</a></li>
        <li><a data-letter="0" href="<?php echo base_url('forums/members/view/')?>/u">?</a></li>
        <li><a data-letter="1" href="<?php echo base_url('forums/members/view/')?>/a">*</a></li>
    </ul>
    <hr />
</div>
<div class="panel-body">
<?php if( empty( $members)): ?>
    <p><?php echo lang('DataEmpty')?></p>
<?php else: ?>
    <?php foreach( $members as $member): ?>
        <?php if( $member ): ?>
        <?php foreach( $member as $sub): ?>
        <div class="col-lg-2 user-pic">
        <?php echo $sub->gravatar ?>
        </div>
        <div class="col-lg-4 fit member-info">
            <p><?php echo lang('Name') ?>: <?php echo sprintf('%s %s', $sub->first_name, $sub->last_name ) ?></p>
            <p><?php echo lang('JoinDate') ?>: <?php echo date('d-m-Y', $sub->created_on) ?></p>
            <p><?php echo lang('Rank') ?>: <?php echo $sub->rank['rank'] ?></p>
            <p><?php echo lang('Rating') ?>: <?php echo $sub->rank['user_xp'] ?> xp</p>
        </div>
        <?php endforeach; ?>
        <div>&nbsp;</div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
</div>
