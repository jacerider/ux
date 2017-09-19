<?php

/**
 * @file
 * Views integration for UX Filters.
 */

use Drupal\Core\Entity\ContentEntityType;
use Drupal\field\FieldStorageConfigInterface;

/**
 * Implements hook_field_views_data_alter().
 *
 * @see views_field_default_views_data()
 */
function ux_filters_field_views_data_alter(array &$data, FieldStorageConfigInterface $field_storage) {
  if ($field_storage->getType() == 'entity_reference') {
    // Adds a new filter type to entity reference fields that provides an entity
    // select or autocomplete field. Hopefully this will be provided by Drupal
    // core soon.
    $entity_manager = \Drupal::entityManager();
    foreach ($data as $table_name => $table_data) {
      $entity_type_id = $field_storage->getTargetEntityTypeId();
      $entity_type = $entity_manager->getDefinition($entity_type_id);
      $field_name = $field_storage->getName();
      $args = [
        '@label' => $entity_type->getLabel(),
        '@field_name' => $field_name,
      ];

      if (isset($table_data[$field_name . '_target_id']['filter'])) {
        $filter = $table_data[$field_name . '_target_id']['filter'];
        $pseudo_field_name = 'ux_filter__' . $entity_type_id . '__' . $field_name;

        $filter['title'] = t('UX Filter (@field_name)', $args);
        $filter['id'] = 'ux_filter_entity_reference';
        $filter['group'] = $entity_type->getLabel();
        $data[$table_name][$pseudo_field_name]['filter'] = $filter;
      }
    }
  }
  if ($field_storage->getType() == 'daterange') {
    $entity_manager = \Drupal::entityManager();
    foreach ($data as $table_name => $table_data) {
      $entity_type_id = $field_storage->getTargetEntityTypeId();
      $entity_type = $entity_manager->getDefinition($entity_type_id);
      $field_name = $field_storage->getName();
      foreach (['value', 'end_value'] as $column_name) {
        if (isset($table_data[$field_name . '_' . $column_name]['filter'])) {
            $args = [
              '@label' => $entity_type->getLabel(),
              '@field_name' => $field_name . ':' . $column_name,
            ];
            $filter = $table_data[$field_name . '_' . $column_name]['filter'];
            $pseudo_field_name = 'ux_filter__' . $entity_type_id . '__' . $field_name . '__' . $column_name;

            $filter['title'] = t('UX Filter (@field_name)', $args);
            $filter['id'] = 'ux_filter_date';
            $filter['group'] = $entity_type->getLabel();
            $data[$table_name][$pseudo_field_name]['filter'] = $filter;
        }
      }
    }
  }
}
