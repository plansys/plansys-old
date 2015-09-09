<div example-field <?= $this->expandAttributes($this->options); ?>>
    <!-- info -->
    <data name="name" class="hide"><?= $this->name ?></data>
    <data name="value" class="hide"><?= $this->value ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <!-- /info -->

    <!-- field -->
    - Example Field -
    <div class="info">
        This FormField does nothing, just an example for creating new FormField<br/>
        <input ng-model='value' type="text" style='text-align:center;'/>
    </div>
    <!-- /field -->
    
    <!-- error -->
    <div ng-if="errors[name]" class="alert error alert-danger">
        {{ errors[name][0]}}
    </div>
    <!-- /error -->
</div>
