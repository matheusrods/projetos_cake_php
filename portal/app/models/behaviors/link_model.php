<?php
class LinkModelBehavior extends ModelBehavior {
    public function linkModel(&$model, $linkto, $foreignKey = null, $conditions = null, $type = 'left') {
        if (!empty($conditions)) {
            $config = array($linkto => array('foreignKey' => false, 'conditions' => $conditions, 'type' => $type));
        } else {
            $config = array($linkto => array('foreignKey' => $foreignKey));
        }
        return $model->bindModel(array('belongsTo' => $config), false);
    }
    
    public function unlinkModel(&$model, $unlinkto) {
        return $model->unbindModel(array('belongsTo' => array($unlinkto)), false);
    }

    public function unlinkAll(&$model) {
        $links = array(
            'hasOne' => array_keys($model->hasOne),
            'hasMany' => array_keys($model->hasMany),
            'belongsTo' => array_keys($model->belongsTo),
            'hasAndBelongsToMany' => array_keys($model->hasAndBelongsToMany)
        );
        foreach($links as $relation => $unlinkto) {
            $model->unbindModel(array($relation => $unlinkto), false);
        }
    }
}