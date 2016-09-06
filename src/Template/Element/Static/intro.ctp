<section class="well">
  <h4><?= __('Maker Manager integrates the various systems that run Dallas Makerspace.') ?></h4>
  <?= __('Features:') ?>
  <?= $this->Html->nestedList([
    __('Allows us to directly link users to their billing account while allowing them to use their usual login information.'),
    __('Request an RFID badge for yourself or your added family members.'),
    __('For family member account holders, it provides a solution for creating Active Directory accounts and self-servicing badges.'),
    __('For administrators, it provides a self documenting solution for adding badges to the access control system along with an easy way to manage them.')
  ]) ?>
</section>