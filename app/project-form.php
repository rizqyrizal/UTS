<?php
$formPrefix = $formPrefix ?? 'project';
?>
<div class="row g-3">
  <div class="col-12">
    <label class="form-label" for="<?= e($formPrefix) ?>-name">Nama project</label>
    <input class="form-control project-field-name" type="text" id="<?= e($formPrefix) ?>-name" name="name" placeholder="Contoh: Rona Skincare" required>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="<?= e($formPrefix) ?>-category">Kategori</label>
    <select class="form-select project-field-category" id="<?= e($formPrefix) ?>-category" name="category">
      <option>Brand Identity</option><option>Campaign</option><option>Digital Experience</option><option>Packaging</option><option>General</option>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="<?= e($formPrefix) ?>-status">Status</label>
    <select class="form-select project-field-status" id="<?= e($formPrefix) ?>-status" name="status">
      <option>Berjalan</option><option>Review</option><option>Pause</option><option>Selesai</option>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label" for="<?= e($formPrefix) ?>-deadline">Deadline</label>
    <input class="form-control project-field-deadline" type="date" id="<?= e($formPrefix) ?>-deadline" name="deadline">
  </div>
  <div class="col-md-6">
    <label class="form-label" for="<?= e($formPrefix) ?>-owner">Pemilik</label>
    <input class="form-control project-field-owner" type="text" id="<?= e($formPrefix) ?>-owner" name="owner" placeholder="Nama penanggung jawab">
  </div>
</div>
