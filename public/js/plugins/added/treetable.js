+function ( $ ) {
    'use strict';

    /**
     * Store reference to plugin with same name.
     */
    var old = $.fn.treeTable;

    /**
     * Internal constants
     */
    var TT = {
        expander: 'treetable-expander',
        expanded: 'treetable-expanded',
        collapsed: 'treetable-collapsed',
        expanderTemplate: '<span class="treetable-expander"></span>'
    };

    /**
     * Public API constructor.
     * Usage: $( selector ).treeTable({ ... })
     */
    function Plugin ( options ) {
        return this.each( function () {
            var $this = $( this );
            var data = $this.data( 'treetable' );

            if ( ! data ) {
                $this.data(
                    'treetable',
                    new TreeTable(
                        this,
                        $.extend(
                            true,
                            $.fn.treeTable.defaults,
                            typeof options == 'object' ? options : {} )
                    ));
            }
        });
    }

    /**
     * API Constructor. Takes in an element selector and an options
     * object and converts the table to be rendered as a tree.
     */
    var TreeTable = function ( element, options ) {
        // Reference to each nodes depth, starts with 0
        this.depths = {};
        // Reference to count of children nodes for each node
        this.children = {};
        // Extended options
        this.options = options;
        this.$table = $( element );
        this.build( this.$table.find( 'tr[data-node^="treetable"]' ) );
    }

    /**
     * Turns the table into a tree, with expand/collapse buttons.
     * This runs in the following steps:
     *   1) Attach event handlers to the toggle buttons
     *   2) Add depth class to each row
     *   3) Insert expand/collapse buttons for rows with children
     *      amd mark initial state (expanded or collapsed)
     */
    TreeTable.prototype.build = function ( nodes ) {
        this.attachEvents();
        this.addDepth( nodes );
        this.addExpanders( nodes );
    };

    /**
     * Iterates over the nodes and adds a CSS class and data attribute
     * for the depth of the node in the tree.
     */
    TreeTable.prototype.addDepth = function ( nodes ) {
        var self = this;

        nodes.each( function ( idx, node ) {
            var $node = $( node );
            var nodeId = $node.data( 'node' );
            var pnodeId = $node.data( 'pnode' );
            var depth = ( pnodeId && pnodeId in self.depths )
                ? self.depths[ pnodeId ] + 1
                : 0;

            // Add a counter to the children if this has a parent
            if ( pnodeId ) {
                self.children[ pnodeId ]++;
            }

            self.children[ nodeId ] = 0;
            $node.data( 'depth', depth );
            self.depths[ nodeId ] = depth;
            $node.addClass( 'treetable-depth-' + depth );
        });
    };

    /**
     * Renders expander buttons to each row with children.
     */
    TreeTable.prototype.addExpanders = function ( nodes ) {
        var self = this;

        nodes.each( function ( idx, node ) {
            var $node = $( node );
            var nodeId = $node.data( 'node' );

            if ( self.children[ nodeId ] > 0 ) {
                $( TT.expanderTemplate )
                    .prependTo( $node.find( 'td' ).get( 0 ) )
                    .addClass( (self.options.startCollapsed)
                        ? self.options.collapsedClass
                        : self.options.expandedClass );
                $node.addClass( (self.options.startCollapsed)
                    ? TT.collapsed
                    : TT.expanded );

                // If the node is to start collapsed, collapse all
                // of this node's children.
                if ( self.options.startCollapsed ) {
                    self.$table.find( 'tr[data-pnode="' + nodeId + '"]' ).hide();
                }
            }
        });
    };

    /**
     * Attaches an event handler to the table for catching all clicks
     * to the expander buttons.
     */
    TreeTable.prototype.attachEvents = function () {
        var self = this;

        this.$table.on( 'click.treetable', '.' + TT.expander, function () {
            var $this = $( this );
            self.toggle( $this, $this.closest( 'tr' ) );
        });
    };

    TreeTable.prototype.toggle = function ( $expander, $node ) {
        var nodeId = $node.data( 'node' );

        $expander.toggleClass( this.options.expandedClass );
        $expander.toggleClass( this.options.collapsedClass );
        $node.toggleClass( TT.collapsed ).toggleClass( TT.expanded );

        if ( $node.hasClass( TT.collapsed ) ) {
            // Hide all descendant nodes and toggle the state of
            // any expander in the descendants.
            this.$table.find( 'tr[data-pnode^="' + nodeId + '"]' )
                .addClass( TT.collapsed )
                .removeClass( TT.expanded )
                .hide();
            this.$table.find( 'tr[data-pnode^="' + nodeId + '"] .' + TT.expander )
                .removeClass( this.options.expandedClass )
                .addClass( this.options.collapsedClass );
        }
        else {
            // Just show the immediate children
            this.$table.find( 'tr[data-pnode="' + nodeId + '"]' ).show();
        }
    };

    $.fn.treeTable = Plugin;
    $.fn.treeTable.defaults = {
        treeColumn: 0,
        startCollapsed: false,
        expandedClass: 'fa fa-angle-down',
        collapsedClass: 'fa fa-angle-right'
    };

    $.fn.treeTable.noConflict = function () {
        $.fn.treeTable = old;
        return this;
    }
}( jQuery );