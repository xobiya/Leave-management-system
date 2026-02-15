import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('appShell', () => ({
	sidebarOpen: false,
	sidebarCollapsed: false,
	darkMode: localStorage.getItem('theme') === 'dark',
	tourOpen: false,
	tourStep: 0,
	tourSteps: [
		{ selector: '[data-tour="sidebar"]', title: 'Workspace navigation', body: 'Switch between Employee, Manager, and Admin workspaces here.' },
		{ selector: '[data-tour="search"]', title: 'Global search', body: 'Search people, requests, and reports instantly.' },
		{ selector: '[data-tour="quick-apply"]', title: 'Quick apply', body: 'Submit time off requests without leaving your dashboard.' },
		{ selector: '[data-tour="approval-queue"]', title: 'Approval queue', body: 'Managers validate requests right from this table.' },
	],
	tourRect: null,
	tourTooltip: { top: 0, left: 0 },
	init() {
		this.$watch('darkMode', (value) => localStorage.setItem('theme', value ? 'dark' : 'light'));
		window.addEventListener('resize', () => { if (this.tourOpen) this.updateTourTarget(); });
		window.addEventListener('scroll', () => { if (this.tourOpen) this.updateTourTarget(); });
	},
	startTour() {
		this.tourStep = 0;
		this.tourOpen = true;
		this.$nextTick(() => this.updateTourTarget());
	},
	nextTour() {
		if (this.tourStep < this.tourSteps.length - 1) {
			this.tourStep++;
			this.$nextTick(() => this.updateTourTarget());
		} else {
			this.tourOpen = false;
		}
	},
	prevTour() {
		if (this.tourStep > 0) {
			this.tourStep--;
			this.$nextTick(() => this.updateTourTarget());
		}
	},
	updateTourTarget() {
		const step = this.tourSteps[this.tourStep];
		const target = document.querySelector(step.selector);

		if (!target) {
			this.tourRect = null;
			return;
		}

		target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });

		const rect = target.getBoundingClientRect();
		const padding = 8;
		this.tourRect = {
			top: rect.top - padding + window.scrollY,
			left: rect.left - padding + window.scrollX,
			width: rect.width + padding * 2,
			height: rect.height + padding * 2,
		};

		this.tourTooltip = {
			top: rect.bottom + 16 + window.scrollY,
			left: rect.left + window.scrollX,
		};
	},
}));

Alpine.start();
