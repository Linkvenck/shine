void function ( exports, $, _, Backbone ) {

	var ES_FrameView = exports.ES_FrameView = B.View.extend({
		constructor: function ES_FrameView () {
			_.bindAll(this, 'update', 'savePosition')
			B.View.apply(this, arguments);
		},
		ready: function () {
			this.$wrapper = this.$('.time-blocks');
			this.$inOut = this.$('.animation-in,.animation-out');
			this.$in = this.$('.animation-in');
			this.$out = this.$('.animation-out');
			this.$wait = this.$('.animation-wait');

			this.$inOut
				.resizable({
					handles: 'w,e',
					minWidth: 0,
					distance: 1,
					//maxHeight: 7,

					//grid: [5,0],
					//snap: '.guide-block',
					//snapTolerance: 5
				})
				.draggable({
					axis: 'x',
					snap: '.guide-block',
					snapTolerance: 5
				})
			this.$wait
				.draggable({
					axis: 'x',
					snap: '.guide-block',
					snapTolerance: 5
				})
			this.update();
			this.checkSelected();
			this.listenTo( this.model, 'change:selected', this.checkSelected )
		},

		events: {
			'mousedown .time-block': function(e) {
				e.preventDefault();
			},
			'resizestart': function(e,ui) {
				var inPos = this.$in.position();
				var outPos = this.$out.position();
				var inWidth = this.$in.width();
				var outWidth = this.$out.width();

				if ($(e.originalEvent.target).is('.ui-resizable-w')) {
					this.$out.resizable('option', 'maxWidth', (outWidth + outPos.left) - (inWidth + inPos.left))
					this.resizeHandle = 'w';
				}
				else {
					this.$out.resizable('option', 'maxWidth', false)
					this.resizeHandle = 'e';
				}
			},
			'resize .animation-in,.animation-out': function(e,ui) {

				var inPos = this.$in.position();
				var outPos = this.$out.position();
				var inWidth = this.$in.width();
				var outWidth = this.$out.width();

				if (ui.position.left < 0 && this.resizeHandle == 'e') {
					ui.size.width += ui.position.left;
					ui.position.left = 0;
				}
				if (ui.size.width < 0 && this.resizeHandle == 'w') {
					ui.position.left += ui.size.width;
					ui.size.width = 0;
				}
				if (ui.helper.is('.animation-in') && (ui.position.left) > outPos.left) {
					ui.size.width = 0;
					ui.position.left = outPos.left;
				}
				this.debounceUpdate()
			},
			'drag .animation-in,.animation-out': function(e,ui) {
				var inPos = this.$in.position();
				var outPos = this.$out.position();
				var inWidth = this.$in.width();
				var outWidth = this.$out.width();

				if (ui.helper.is('.animation-out') && ui.position.left < (inPos.left + inWidth)) {
					ui.position.left = inPos.left + inWidth
				}
				if (ui.helper.is('.animation-in') && (ui.position.left + inWidth) > outPos.left) {
					ui.position.left = outPos.left - inWidth
				}
				if (ui.position.left < 0)
					ui.position.left = 0;
				this.debounceUpdate()
			},
			'drag .animation-wait': function(e,ui) {
				if (ui.position.left < 0)
					ui.position.left = 0;
				this.$in.css('left', ui.position.left);
				this.$out.css('left', ui.position.left + this.$wait.width())
			},
			'dragstop': 'debounceSave',
			'resizestop': 'debounceSave',
			'mousedown': function(e){
				e.stopPropagation();
				!this.model.get('selected') && this.model.collection.invoke('set', 'selected', false);
				this.model.set('selected', true);
				this.rootView.itemInspector.showTab('animation');
			}
		},
		modelEvents: {
			'change:animation.in.delay': 'debounceUpdate',
			'change:animation.in.duration': 'debounceUpdate',
			'change:animation.out.delay': 'debounceUpdate',
			'change:animation.out.duration': 'debounceUpdate',
		},
		bindings: [
			{
				selector: '.animation-in',
				type: 'style',
				attr: {
					'left': 'animation.in',
					'width': 'animation.in'
				},
				parse: function ( animation, attr ) {
					switch (attr) {
						case 'left':
							return animation.delay / 10 + 'px';
						case 'width':
							return animation.duration / 10 + 'px';
					}
				}
			},
			{
				selector: '.animation-out',
				type: 'style',
				attr: {
					'left': 'animation.out',
					'width': 'animation.out',
				},
				parse: function ( animation, attr ) {
					switch (attr) {
						case 'left':
							return animation.delay / 10 + 'px';
						case 'width':
							return animation.duration / 10 + 'px';
					}
				}
			},
		],
		checkSelected: function(){
			if (this.model.get('selected') ) {
				this.$el.addClass('selected');
			}
			else {
				this.$el.removeClass('selected');
			}
		},
		debounceUpdate: function() {
			requestAnimationFrame(this.update);
		},
		debounceSave: function() {
			requestAnimationFrame(this.savePosition);
		},
		update: function() {
			var inPos = this.$in.position();
			var outPos = this.$out.position();
			var waitLeft = inPos.left;
			var waitWidth = outPos.left - inPos.left;

			this.$wait.css({
				left: waitLeft,
				width: waitWidth
			})
			this.$in.resizable('option','maxWidth', outPos.left - inPos.left);
		},
		savePosition: function() {
			var inDelay = Math.round(this.$in.position().left / 1) * 10;
			var inDuration = Math.round(this.$in.width() / 1) * 10;
			var outDelay = Math.round(this.$out.position().left / 1) * 10;
			var outDuration = Math.round(this.$out.width() / 1) * 10;
			this.model.set({
				animation: {
					'out': {
						delay: outDelay,
						duration: outDuration
					},
					'in': {
						delay: inDelay,
						duration: inDuration,
					},
				}
			})
		}
	});

	var ES_FramesView = exports.ES_FramesView = B.CollectionView.extend({
		constructor: function ES_FramesView () {
			B.CollectionView.apply(this, arguments);
		},
		initialize: function() {
			this.on('set:collection', function( collection ) {
				this.listenTo(collection, 'remove', this.reset);
			})
		},
		itemView: ES_FrameView,
		_reverseOrder: true,
		events: {
			'scroll': function(e) {
				this.rootView.timelineView.$slider.get(0).scrollLeft = this.el.scrollLeft;
				this.rootView.layersView.el.scrollTop = this.el.scrollTop;
				this.rootView.timelineView.$guide.css('transform','translateX('+ (-this.el.scrollLeft) +'px)')
			}
		}
	});

}(this, jQuery, _, JSNES_Backbone);