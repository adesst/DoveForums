<div class="panel-body">
        <ul class="alphabet-nav">
            <li><a data-letter="A" href="/directory/agent/A">A</a></li>
            <li><a data-letter="B" href="/directory/agent/B">B</a></li>
            <li><a data-letter="C" href="/directory/agent/C">C</a></li>
            <li><a data-letter="D" href="/directory/agent/D">D</a></li>
            <li><a data-letter="E" href="/directory/agent/E">E</a></li>
            <li><a data-letter="F" href="/directory/agent/F">F</a></li>
            <li><a data-letter="G" href="/directory/agent/G">G</a></li>
            <li><a data-letter="H" href="/directory/agent/H">H</a></li>
            <li><a data-letter="I" href="/directory/agent/I">I</a></li>
            <li><a data-letter="J" href="/directory/agent/J">J</a></li>
            <li><a data-letter="K" href="/directory/agent/K">K</a></li>
            <li><a data-letter="L" href="/directory/agent/L">L</a></li>
            <li><a data-letter="M" href="/directory/agent/M">M</a></li>
            <li><a data-letter="N" href="/directory/agent/N">N</a></li>
            <li><a data-letter="O" href="/directory/agent/O">O</a></li>
            <li><a data-letter="P" href="/directory/agent/P">P</a></li>
            <li><a data-letter="Q" href="/directory/agent/Q">Q</a></li>
            <li><a data-letter="R" href="/directory/agent/R">R</a></li>
            <li><a data-letter="S" href="/directory/agent/S">S</a></li>
            <li><a data-letter="T" href="/directory/agent/T">T</a></li>
            <li><a data-letter="U" href="/directory/agent/U">U</a></li>
            <li><a data-letter="V" href="/directory/agent/V">V</a></li>
            <li><a data-letter="W" href="/directory/agent/W">W</a></li>
            <li><a data-letter="X" href="/directory/agent/X">X</a></li>
            <li><a data-letter="Y" href="/directory/agent/Y">Y</a></li>
            <li><a data-letter="Z" href="/directory/agent/Z">Z</a></li>
            <li><a data-letter="0" href="/directory/agent/u">?</a></li>
            <li><a data-letter="1" href="/directory/agent/a">*</a></li>
        </ul>
        <hr />
</div>
<div class="panel-body">
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
</div>
