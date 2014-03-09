<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Authorize;

class Rule {

    /**
     * Behavior of Rule.
     *
     * If true then can do things
     * If false then cannot do things.
     *
     * @var boolean
     */
    protected $base_behavior;

    /**
     * @var array
     */
    protected $actions = array();

    /**
     * @var array
     */
    protected $conditions = array();

    public $expanded_actions;

    private $match_all;
    private $subjects;
    private $block;

    public function __construct($base_behavior, $action, $subject, $conditions, $block)
    {
        $this->match_all = (null === $action) && (null === $subject);
        $this->base_behavior = $base_behavior;
        $this->actions[] = $action;
        $this->subjects[] = $subject;
        $this->conditions = (null === $conditions) ? array() : $conditions;
        $this->block = $block;
    }

    public function is_relevant($action, $subject)
    {
        if (is_array($subject)) {
            $subject = current($subject);
        }

        return $this->match_all
            || ($this->matches_action($action) && $this->matches_subject($subject));
    }

    public function matches_conditions($action, $subject, $extra_args)
    {
        if ($this->match_all) {

            return $this->call_block_with_all($action, $subject, $extra_args);
        } elseif ( $this->block && !$this->subject_class($subject) ) {

            $block = $this->block;

            return $block($subject, $extra_args);
        } elseif ( is_array($this->conditions) && is_array($subject) ) {

            return $this->nested_subject_matches_conditions($subject);

        } elseif (   is_array($this->conditions)
                  && !$this->subject_class($subject)
        ) {

            return $this->matches_conditions_hash($subject);
        } else {

            # Don't stop at "cannot" definitions when there are conditions.
            return empty( $this->conditions ) ? true : $this->base_behavior;
        }
    }

    public function isOnlyBlock()
    {
        return $this->isConditionsEmpty() && (null !== $this->block);
    }

    public function isOnlyRawSql()
    {
        return null === $this->block
            && !$this->isConditionsEmpty()
            && !is_array($this->getConditions());
    }

    public function isConditionsEmpty()
    {
        return is_array($this->conditions) || ( null === $this->conditions );
    }

    public function getAssociationsHash($conditions = null)
    {
        $conditions = null === $conditions ? $this->conditions : $conditions;

        $hash = array();
        if (is_array($conditions)) {

            foreach ( $conditions as $name => $value ) {
                if (is_array($value)) {
                    $hash[$name] = $this->getAssociationsHash($value);
                } else {
                    $hash[$name] = $value;
                }
            }

        }

        return $hash;
    }

    public function getAttributesForConditions()
    {
        $attributes = array();
        if ( is_array( $conditions)  ) {

            foreach ( $this->conditions as $key => $value ){
                if( !is_array($value) ) {
                    $attributes[$key] = $value;
                }
            }

        }

        return $attributes;
    }

    public function hasBaseBehavior()
    {
        return $this->base_behavior;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    private function subject_class($subject)
    {
        $klass = is_array($subject) ? current($subject) : $subject;

        return is_string($klass);
    }

    private function matches_action($action)
    {
        return in_array('manage', $this->expanded_actions)
            || in_array( $action, $this->expanded_actions);
    }

    private function matches_subject($subject)
    {
        return in_array('all', $this->subjects)
            || in_array($subject, $this->subjects)
            || $this->matches_subject_class($subject);
    }

    private function matches_subject_class($subject)
    {
        if ( is_object($subject) ) {
            return in_array(get_class($subject), $this->subjects);
        }
        return false;
    }

    private function matches_conditions_hash($subject, $conditions = null)
    {
        $conditions = null === $conditions ? $this->conditions : $conditions;
        if ( empty($conditions) ) {
            return true;
        } else {
            foreach ( $conditions as $name => $value) {
                $attribute = $subject->$name;
                if ( is_array($value) ) {
                    if ( is_array($attribute) || $attribute instanceof \ArrayAccess ){
                        foreach ($attribute as $element) {
                            if (true === $this->matches_conditions_hash($element, $value))
                                return true;
                        }
                        return false;
                    } else {
                        return $this->matches_conditions_hash($attribute, $value);
                    }
                } else {
                    return $attribute == $value;
                }
            }
        }
    }

    private function nested_subject_matches_conditions($subject_hash)
    {
        $parent = key($subject_hash);
        $child = current($hash);
        return $this->matches_conditions_hash($parent, isset($this->conditions[strtolower($parent)]) ? $this->conditions[strtolower($parent)] : array() );
    }

    private function call_block_with_all($action, $subject, $extra_args = array())
    {
        $block = $this->block;
        if (is_object($subject)) {
            return $block($action, get_class($subject), $subject, $extra_args);
        } else {
            return $block($action, $subject, null, $extra_args);
        }
    }

}
