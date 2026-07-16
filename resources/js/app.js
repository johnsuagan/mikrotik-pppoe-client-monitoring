import './bootstrap';

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';
import 'admin-lte';

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
window.Chart = Chart;

const beatPlugin = {
    id: 'beatPlugin',
    afterDatasetDraw(chart, args) {
        const { ctx } = chart;
        const meta = chart.getDatasetMeta(args.index);
        if (!meta || !meta.data || meta.data.length < 1) return;

        const lastPoint = meta.data[meta.data.length - 1];
        if (!lastPoint || lastPoint.x === undefined || lastPoint.y === undefined) return;

        const borderColor = chart.data.datasets[args.index].borderColor;
        const color = borderColor || 'rgba(80,70,229,1)';
        const pulsePhase = (Date.now() % 1400) / 1400;
        const pulse = Math.sin(pulsePhase * Math.PI * 2) * 0.5 + 0.5;
        const outerRadius = 8 + pulse * 6;
        const innerRadius = 4 + pulse * 2;

        function withAlpha(c, a) {
            if (c.startsWith('rgba(')) {
                return c.replace(/,\s*[\d.]+\)$/, ', ' + a.toFixed(2) + ')');
            }
            if (c.startsWith('rgb(')) {
                return c.replace('rgb(', 'rgba(').replace(')', ', ' + a.toFixed(2) + ')');
            }
            if (c.startsWith('#')) {
                const r = parseInt(c.slice(1, 3), 16);
                const g = parseInt(c.slice(3, 5), 16);
                const b = parseInt(c.slice(5, 7), 16);
                return 'rgba(' + r + ',' + g + ',' + b + ',' + a.toFixed(2) + ')';
            }
            return c;
        }

        ctx.save();

        ctx.beginPath();
        ctx.arc(lastPoint.x, lastPoint.y, outerRadius, 0, Math.PI * 2);
        ctx.fillStyle = withAlpha(color, 0.15 + pulse * 0.15);
        ctx.fill();
        ctx.closePath();

        ctx.beginPath();
        ctx.arc(lastPoint.x, lastPoint.y, innerRadius, 0, Math.PI * 2);
        ctx.fillStyle = withAlpha(color, 0.5 + pulse * 0.5);
        ctx.fill();
        ctx.closePath();

        ctx.beginPath();
        ctx.arc(lastPoint.x, lastPoint.y, 3, 0, Math.PI * 2);
        ctx.fillStyle = color;
        ctx.fill();
        ctx.closePath();

        ctx.restore();
    }
};

Chart.register(beatPlugin);

let beatAnimFrame = null;
function startBeatLoop() {
    function tick() {
        Chart.instances && Object.values(Chart.instances).forEach(c => {
            if (c && c.canvas && c.canvas.offsetParent !== null) {
                c.draw();
            }
        });
        beatAnimFrame = requestAnimationFrame(tick);
    }
    tick();
}
startBeatLoop();

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark-mode');
        document.body.classList.add('dark-mode');
    }

    const toggle = document.getElementById('themeToggle');
    if (toggle) {
        toggle.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark-mode');
            document.body.classList.toggle('dark-mode', isDark);
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (window.__updateChartsTheme) window.__updateChartsTheme();
        });
    }
});

window.parseRate = function(rateStr) {
    if (rateStr === null || rateStr === undefined || rateStr === '') return 0;
    if (rateStr === '0bps') return 0;
    if (typeof rateStr === 'number') return rateStr * 1000;
    if (typeof rateStr === 'string' && /^\d+(\.\d+)?$/.test(rateStr.trim())) return parseFloat(rateStr) * 1000;
    const match = String(rateStr).match(/([\d.]+)\s*(bit|kbps|Mbps|Gbps|bps)/i);
    if (!match) return 0;
    const val = parseFloat(match[1]);
    const unit = match[2].toLowerCase();
    switch(unit) {
        case 'gbps': return val * 1000000;
        case 'mbps': return val * 1000;
        case 'kbps': return val * 1000;
        case 'bps': return val;
        case 'bit': return val;
        default: return val;
    }
};

window.formatRate = function(bps) {
    if (bps >= 1000000) return (bps / 1000000).toFixed(1) + ' Mbps';
    if (bps >= 1000) return (bps / 1000).toFixed(1) + ' kbps';
    return bps.toFixed(0) + ' bps';
};

window.getChartColors = function() {
    const isDark = document.body.classList.contains('dark-mode');
    return {
        grid: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
        text: isDark ? '#94a3b8' : '#64748b',
        download: 'rgba(80,70,229,0.85)',
        downloadBg: 'rgba(80,70,229,0.08)',
        upload: 'rgba(8,145,178,0.85)',
        uploadBg: 'rgba(8,145,178,0.08)',
        bg: isDark ? '#111827' : '#ffffff',
    };
};

window.updateChartTheme = function(chart) {
    const c = window.getChartColors();
    if (chart.options.scales?.x) {
        chart.options.scales.x.grid.color = c.grid;
        chart.options.scales.x.ticks.color = c.text;
    }
    if (chart.options.scales?.y) {
        chart.options.scales.y.grid.color = c.grid;
        chart.options.scales.y.ticks.color = c.text;
    }
    chart.update('none');
};
