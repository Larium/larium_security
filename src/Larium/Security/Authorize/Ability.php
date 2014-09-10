<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Authorize;

class Ability
{

    protected $rules = array();

    protected $default_alias_actions = array(
        'read' => array('index', 'show'),
        'create' => array('new'),
        'update' => array('edit'),
        'destroy' => array('delete')
    );

    protected $aliased_actions = array();

    public function canDo($action, $subject, $extra_args = array())
    {
        $match = null;
        $relevant_rules_for_match = $this->relevant_rules_for_match($action, $subject);
        foreach ( $relevant_rules_for_match as $rule) {
            if ( $rule->matches_conditions($action, $subject, $extra_args) ){
                $match = $rule;
                break;
            }
        }
        return isset($match) ? $match->hasBaseBehavior() : false;
    }

    public function cannotDo($action, $subject, $extra_args = array())
    {
        return !$this->canDo($action, $subject, $extra_args);
    }

    public function can($action = null, $subject = null, $conditions = null, $block = null)
    {
        $this->rules[] = new Rule(true, $action, $subject, $conditions, $block);
    }

    public function cannot($action = null, $subject = null, $conditions = null, $block=null)
    {
        $this->rules[] = new Rule(false, $action, $subject, $conditions, $block);
    }

    /**
     * Alias one or more actions into another one.
     *
     * <code>
     *      $ability->setAliasAction(['update', 'destroy', 'to'=>'modify']);
     *      $ability->can('modify', 'Comment');
     * </code>
     *
     * Then modify permission will apply to both 'update' and 'destroy'
     * requests.
     *
     * <code>
     *      $ability->canDo('update', 'Comment'); // returns true
     *      $ability->canDo('destroy', 'Comment'); // returns true
     * </code>
     *
     * This only works in one direction. Passing the aliased action into the "canDo" method
     * will not work because aliases are meant to generate more generic actions.
     * <code>
     *      $ability->setAliasAction(['update', 'destroy', 'to' => 'modify'])
     *      $ability->can('update', 'Comment');
     *      $ability->canDo('modify', 'Comment'); // return false
     * </code>
     *
     * Unless that exact alias is used.
     *
     * <code>
     *      $ability->can('modify', 'Comment');
     *      $ability->canDo('modify', 'Comment'); // returns true
     * </code>
     *
     * The following aliases are added by default for conveniently mapping common controller actions.
     * <code>
     *      $this->ability->setAliasAction(['index', 'show', 'to' => 'read']);
     *      $this->ability->setAliasAction(['new', 'to' => 'create']);
     *      $this->ability->setAliasAction(['edit', 'to' => 'update']);
     * </code>
     *
     * @param array $args An array in format ['action', 'other_action',... ,'to'=>'alias_action'].
     *                    You can add as many actions you want but you must set
     *                    the alias action in last element by setting the value
     *                    of the key to `to`.
     *
     * @return void
     */
    public function setAliasAction(array $args)
    {
        $target = array_pop($args); // grab element with 'to' key

        if (!isset($this->aliased_actions[$target])) {
            $this->aliased_actions[$target] = array();
        }
        $this->aliased_actions[$target] = array_merge($this->aliased_actions[$target], $args);
    }

    public function getAliasedActions()
    {
        return empty( $this->aliased_actions )
            ? $this->default_alias_actions
            : array_merge_recursive($this->default_alias_actions, $this->aliased_actions);
    }

    public function clearAliasedActions()
    {
        $this->aliased_actions = array();
    }

    public function authorize($action, $subject, $args=array())
    {
        if ($this->cannotDo($action, $subject, $args)) {

            $message = null;
            if (is_array(end($args)) && array_key_exists('message', end($args))) {
                $message = array_pop($args);
            }

            $message = null != $message
                ? $message
                : $this->getUnauthorizedMessage($action, $subject);

            throw new AccessDenied($message, $action, $subject);
        }
    }

    public function getUnauthorizedMessage($action, $subject)
    {

    }

    public function getAttributesFor($action, $subject)
    {
        $attributes = array();
        $relevant_rules = $this->relevant_rules($action, $subject);
        foreach ($relevant_rules as $rule) {
            if ($rule->hasBaseBehavior())
                $attributes = array_merge(
                    $attributes,
                    $rule->getAttributesForConditions()
                );
        }

        return $attributes;
    }

    public function hasBlock($action, $subject)
    {
        $relevant_rules = $this->relevant_rules($action, $subject);
        foreach ($relevant_rules as $rule) {
            if( $rule->only_block() )
                return true;
        }
    }

    public function hasRawSql($action, $subject)
    {
        $relevant_rules = $this->relevant_rules($action, $subject);
        foreach ($relevant_rules as $rule) {
            if( $rule->isOnlyRawSql() )
                return true;
        }
    }

    /**
     * Private
     */

    private function unauthorized_message_keys($action, $subject)
    {

    }

    private function expand_actions(array $actions )
    {
        $map = array();
        $aliased_actions = $this->getAliasedActions();
        foreach ($actions as $action) {
            if ( isset( $aliased_actions[$action] ) ){
                $expand_actions = $this->expand_actions( $aliased_actions[$action] );
                $map = array_merge( $map, $expand_actions );
            }
            $map = array_merge($map, array($action) );
        }

        return $map;
    }

    private function aliases_for_action($action)
    {
        $results = array($action);
        $aliased_actions = $this->aliased_actions();
        foreach( $aliased_actions as $aliased_action => $actions ){
            if ( in_array($action, $actions) ){
                $results[] = $this->aliases_for_action($aliased_action);
            }
        }
        return $results;
    }

    private function rules()
    {
        return null === $this->rules ? array() : $this->rules;
    }

    private function relevant_rules($action, $subject)
    {
        $rules = array_reverse( $this->rules() );
        $match = array();

        foreach( $rules as $rule ) {
            // var_dump($rule->actions());
            $rule->expanded_actions = $this->expand_actions($rule->getActions());
            // echo '<pre>';
            // print_r($rule->expanded_actions);
            // echo '</pre>';
            if ( $rule->is_relevant($action, $subject) ){
                $match[] = $rule;
            }
        }

        return $match;
    }

    private function relevant_rules_for_match($action, $subject)
    {
        $relevant_rules = $this->relevant_rules($action, $subject);
        foreach( $relevant_rules as $rule ) {
            if ( $rule->isOnlyRawSql() ) {
                throw new Error("The canDo and cannotDo method cannot be used with a raw sql 'can' definition. The checking code cannot be determined for {$action} {$subject}");
            }
        }
        return $relevant_rules;
    }

    private function relevant_rules_for_query($action, $subject)
    {
        $relevant_rules = $this->relevant_rules($action, $subject);
        foreach( $relevant_rules as $rule ) {
            if ( $rule->isOnlyBlock() ) {
                throw new Error("The accessible_by call cannot be used with a block 'can' definition. The SQL cannot be determined for {$action} {$subject}");
            }
        }
        return $relevant_rules;
    }

}
?>
