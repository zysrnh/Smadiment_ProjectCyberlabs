@extends('mk.layouts.app')

@section('title', 'Data Overview - SMADIMENT')

@section('styles')
    <style>
        /* ══════════════════════════════════════════════════════
               DESIGN TOKENS
            ══════════════════════════════════════════════════════ */
        :root {
            --do-primary: var(--bs-primary, #4361EE);
            --do-primary-rgb: var(--bs-primary-rgb, 67, 97, 238);
            --do-primary-lt: rgba(var(--do-primary-rgb, 67, 97, 238), .10);
            --do-green: #10B981;
            --do-green-lt: #ECFDF5;
            --do-red: #EF4444;
            --do-red-lt: #FEF2F2;
            --do-slate-50: #F8FAFC;
            --do-slate-100: #F1F5F9;
            --do-slate-200: #E2E8F0;
            --do-slate-300: #CBD5E1;
            --do-slate-400: #94A3B8;
            --do-slate-500: #64748B;
            --do-slate-700: #334155;
            --do-slate-800: #1E293B;
            --do-slate-900: #0F172A;
            --do-radius: 8px;
            --do-radius-sm: 5px;
            --do-shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
            --do-shadow-md: 0 4px 14px rgba(15, 23, 42, .08);
            --do-shadow-lg: 0 10px 30px rgba(15, 23, 42, .12);

            --c-news: #0284c7;
            --c-twitter: #1d9bf0;
            --c-facebook: #1877f2;
            --c-instagram: #e1306c;
            --c-youtube: #ff0000;
            --c-tiktok: #111827;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        /* ══ Animations ══ */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @keyframes overlayIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes overlayOut {
            from {
                opacity: 1
            }

            to {
                opacity: 0
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .fade-up {
            animation: fadeUp .36s ease-out both;
        }

        .fade-up-d1 {
            animation-delay: .04s;
        }

        .fade-up-d2 {
            animation-delay: .08s;
        }

        .fade-up-d3 {
            animation-delay: .12s;
        }

        /* ══ Grid rows ══ */
        .do-row-top {
            display: grid;
            grid-template-columns: 1fr 1fr 360px;
            gap: 14px;
            margin-bottom: 14px;
            align-items: start;
        }

        .do-row-mid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 14px;
            margin-bottom: 14px;
            align-items: stretch;
        }

        .do-mb14 {
            margin-bottom: 14px;
        }

        /* ══ Tables ══ */
        .do-tbl {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 12px;
        }

        .do-tbl th {
            padding: 0 0 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 1px solid var(--do-slate-200);
        }

        .do-tbl td {
            padding: 8px 0;
            border-bottom: 1px solid var(--do-slate-100);
            vertical-align: middle;
        }

        .do-tbl tbody tr:last-child td {
            border-bottom: none;
        }

        .do-tbl tbody tr:hover td {
            background: var(--do-slate-50);
        }

        .do-tbl-rank {
            font-weight: 800;
            color: var(--do-primary);
            width: 22px;
            font-size: 11px;
        }

        .do-tbl-name {
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
        }

        .do-tbl-num {
            text-align: right;
            font-weight: 700;
            font-size: 11px;
            color: var(--do-slate-500);
        }

        .topic-link {
            color: var(--do-slate-800);
            text-decoration: none;
            transition: color .14s;
        }

        .topic-link:hover {
            color: var(--do-primary);
        }

        /* ══ View-all button ══ */
        .do-view-all {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: transparent;
            color: var(--do-primary);
            border: 1px solid var(--do-primary);
            border-radius: var(--do-radius-sm);
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all .14s;
        }

        .do-view-all:hover {
            background: var(--do-primary);
            color: #fff;
        }

        /* ══ Mention card ══ */
        .do-mention-body {
            display: flex;
            align-items: stretch;
            min-height: 240px;
        }

        .do-mention-chart {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            min-width: 0;
        }

        #chMentionPie {
            width: 100% !important;
            max-width: 200px;
            height: 200px !important;
        }

        .do-mention-stats {
            width: 148px;
            flex-shrink: 0;
            border-left: 1px solid var(--do-slate-200);
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 14px;
        }

        .do-mstat-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 2px;
        }

        .do-mstat-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
            cursor: pointer;
            border-radius: var(--do-radius-sm);
            padding: 5px 6px;
            margin: -5px -6px;
            transition: background .13s;
        }

        .do-mstat-row:hover {
            background: var(--do-primary-lt);
        }

        .do-mstat-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--do-slate-500);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .do-mstat-name span {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .do-mstat-val {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -.5px;
            color: var(--do-slate-900);
            line-height: 1.1;
        }

        .do-mstat-divider {
            height: 1px;
            background: var(--do-slate-100);
        }

        .do-mstat-total-lbl {
            font-size: 10px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .do-mstat-total-val {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--do-primary);
            line-height: 1.1;
        }

        /* ══ SOV card ══ */
        .do-sov-body {
            display: flex;
            align-items: stretch;
            min-height: 300px;
        }

        .do-sov-chart {
            flex: 0 0 230px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 14px 20px 20px;
        }

        #chSovPie {
            width: 200px !important;
            height: 200px !important;
        }

        .do-sov-legend {
            flex: 1;
            border-left: 1px solid var(--do-slate-200);
            padding: 16px 16px 16px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .do-sov-legend-title {
            font-size: 10px;
            font-weight: 800;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .7px;
            margin-bottom: 8px;
            padding-bottom: 7px;
            border-bottom: 2px solid var(--do-slate-100);
        }

        .do-sov-item {
            display: flex;
            flex-direction: column;
            padding: 5px 5px;
            border-radius: var(--do-radius-sm);
            transition: background .13s;
            cursor: pointer;
            gap: 3px;
        }

        .do-sov-item:hover {
            background: var(--do-primary-lt);
        }

        .do-sov-item-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .do-sov-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .do-sov-name {
            flex: 1;
            font-size: 11px;
            font-weight: 600;
            color: var(--do-slate-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .do-sov-pct {
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
            letter-spacing: -.3px;
        }

        .do-sov-bar-wrap {
            height: 4px;
            background: var(--do-slate-100);
            border-radius: 2px;
            overflow: hidden;
        }

        .do-sov-bar {
            height: 100%;
            border-radius: 2px;
            transition: width .8s cubic-bezier(.4, 0, .2, 1);
        }

        /* ══ Map ══ */
        .do-map-wrap {
            display: flex;
        }

        .do-map-area {
            flex: 1;
            min-width: 0;
            position: relative;
        }

        .do-loc-panel {
            width: 210px;
            flex-shrink: 0;
            border-left: 1px solid var(--do-slate-200);
            display: flex;
            flex-direction: column;
        }

        .do-loc-title {
            padding: 11px 14px 8px;
            font-size: 10px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid var(--do-slate-100);
        }

        .do-loc-list {
            overflow-y: auto;
            flex: 1;
            max-height: 400px;
        }

        .do-loc-list::-webkit-scrollbar {
            width: 3px;
        }

        .do-loc-list::-webkit-scrollbar-thumb {
            background: var(--do-slate-200);
            border-radius: 99px;
        }

        .do-loc-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            cursor: pointer;
            border-bottom: 1px solid var(--do-slate-50);
            transition: all .13s;
        }

        .do-loc-item:hover {
            background: rgba(67, 97, 238, .05);
        }

        .do-loc-item.active {
            background: var(--do-primary-lt);
            border-left: 3px solid var(--do-primary);
            padding-left: 9px;
        }

        .do-loc-rank {
            font-size: 10px;
            font-weight: 700;
            color: var(--do-primary);
            width: 16px;
            flex-shrink: 0;
        }

        .do-loc-info {
            flex: 1;
            min-width: 0;
        }

        .do-loc-name {
            font-size: 11px;
            font-weight: 600;
            color: var(--do-slate-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .do-loc-count {
            font-size: 10px;
            color: var(--do-slate-400);
            font-weight: 500;
        }

        .do-loc-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
            background: var(--do-primary);
        }

        /* ══ Skeleton ══ */
        .sk-block {
            border-radius: 4px;
            background: linear-gradient(90deg, var(--do-slate-100) 25%, var(--do-slate-200) 50%, var(--do-slate-100) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
        }

        .sk-overlay {
            position: absolute;
            inset: 0;
            z-index: 3;
            border-radius: var(--do-radius-sm);
        }

        .do-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 36px 16px;
            gap: 7px;
        }

        .do-empty i {
            font-size: 32px;
            color: var(--do-slate-300);
        }

        .do-empty-txt {
            font-size: 12px;
            font-weight: 600;
            color: var(--do-slate-400);
        }

        /* ══ body-scroll utility ══ */
        .do-body-scroll {
            max-height: 210px;
            overflow-y: auto;
        }

        .do-body-scroll::-webkit-scrollbar {
            width: 3px;
        }

        .do-body-scroll::-webkit-scrollbar-thumb {
            background: var(--do-slate-200);
            border-radius: 99px;
        }

        /* ══ Export Dropdown ══ */
        .do-export-wrap {
            position: relative;
        }

        .do-export-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: var(--do-slate-800);
            color: #fff;
            border: none;
            border-radius: var(--do-radius-sm);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: filter .14s;
            box-shadow: var(--do-shadow-sm);
        }

        .do-export-btn:hover {
            filter: brightness(1.15);
        }

        .do-export-btn i {
            font-size: 14px;
        }

        .do-export-dd {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            background: #fff;
            border: 1px solid var(--do-slate-200);
            border-radius: var(--do-radius);
            box-shadow: var(--do-shadow-lg);
            padding: 5px;
            min-width: 200px;
            z-index: 5000;
            display: none;
            animation: fadeUp .16s ease-out;
        }

        .do-export-dd.show {
            display: block;
        }

        .do-export-dd-section {
            padding: 5px 9px 4px;
            font-size: 10px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .do-export-dd-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: var(--do-radius-sm);
            background: transparent;
            border: none;
            width: 100%;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--do-slate-700);
            cursor: pointer;
            transition: background .12s;
        }

        .do-export-dd-btn:hover {
            background: var(--do-primary-lt);
            color: var(--do-primary);
        }

        .do-export-dd-btn i {
            font-size: 14px;
            width: 16px;
        }

        .do-export-dd-divider {
            height: 1px;
            background: var(--do-slate-200);
            margin: 4px 0;
        }

        /* ══ Slide Panel ══ */
        .do-panel-overlay {
            position: fixed;
            inset: 0;
            z-index: 9000;
            background: rgba(15, 23, 42, .45);
            backdrop-filter: blur(4px);
            display: none;
        }

        .do-panel-overlay.show {
            display: block;
            animation: overlayIn .22s ease-out;
        }

        .do-panel-overlay.hiding {
            animation: overlayOut .22s ease-out forwards;
        }

        .do-panel {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            z-index: 9001;
            width: 480px;
            max-width: 100vw;
            background: #fff;
            display: none;
            flex-direction: column;
            border-left: 1px solid var(--do-slate-200);
            box-shadow: -8px 0 40px rgba(15, 23, 42, .16);
        }

        .do-panel.show {
            display: flex;
            animation: slideInRight .28s cubic-bezier(.4, 0, .2, 1);
        }

        .do-panel.hiding {
            animation: slideOutRight .24s cubic-bezier(.4, 0, .2, 1) forwards;
        }

        .do-panel-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--do-slate-200);
            background: var(--do-slate-50);
            flex-shrink: 0;
        }

        .do-panel-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .do-panel-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--do-slate-900);
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .do-panel-count {
            display: none;
        }

        .do-panel-close {
            width: 28px;
            height: 28px;
            border-radius: var(--do-radius-sm);
            border: 1px solid var(--do-slate-200);
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--do-slate-500);
            font-size: 16px;
            transition: all .14s;
            flex-shrink: 0;
        }

        .do-panel-close:hover {
            background: var(--do-red);
            border-color: var(--do-red);
            color: #fff;
        }

        .do-panel-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            border-bottom: 1px solid var(--do-slate-200);
            background: #fff;
            flex-shrink: 0;
        }

        .do-panel-meta {
            flex: 1;
            font-size: 10px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .5px;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .do-panel-tabs {
            display: flex;
            background: var(--do-slate-100);
            border: 1px solid var(--do-slate-200);
            border-radius: var(--do-radius-sm);
            padding: 2px;
            gap: 2px;
        }

        .do-panel-tab {
            padding: 3px 9px;
            border-radius: 3px;
            border: none;
            background: transparent;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all .13s;
            color: var(--do-slate-500);
            font-family: inherit;
        }

        .do-panel-tab:hover {
            background: #fff;
        }

        .do-panel-tab.active {
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }

        .do-panel-tab.active[data-s="all"] {
            color: var(--do-primary);
        }

        .do-panel-tab.neg.active {
            color: var(--do-red);
        }

        .do-panel-tab.pos.active {
            color: #0ea5e9;
        }

        .do-panel-tab.neu.active {
            color: var(--do-slate-500);
        }

        .do-panel-export {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: var(--do-primary);
            color: #fff;
            border: none;
            border-radius: var(--do-radius-sm);
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: filter .13s;
            font-family: inherit;
        }

        .do-panel-export:hover {
            filter: brightness(1.1);
        }

        .do-panel-export i {
            font-size: 12px;
        }

        .do-panel-list {
            overflow-y: auto;
            flex: 1;
            padding: 2px 0;
            min-height: 0;
        }

        .do-panel-list::-webkit-scrollbar {
            width: 4px;
        }

        .do-panel-list::-webkit-scrollbar-thumb {
            background: var(--do-slate-200);
            border-radius: 99px;
        }

        .do-panel-item {
            display: flex;
            gap: 10px;
            padding: 10px 14px;
            border-bottom: 1px solid var(--do-slate-50);
            cursor: pointer;
            transition: background .1s;
            align-items: flex-start;
        }

        .do-panel-item:hover {
            background: #f0f9ff;
        }

        .do-panel-item:last-child {
            border-bottom: none;
        }

        .do-panel-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            color: #fff;
            border: 1.5px solid var(--do-slate-200);
            overflow: hidden;
        }

        .do-panel-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .do-panel-item-body {
            flex: 1;
            min-width: 0;
        }

        .do-panel-author {
            font-size: 12px;
            font-weight: 700;
            color: var(--do-slate-900);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .do-panel-handle {
            font-size: 10px;
            color: var(--do-slate-400);
            font-weight: 500;
            margin-bottom: 2px;
        }

        .do-panel-text {
            font-size: 11px;
            color: var(--do-slate-600);
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .do-panel-footer {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            color: var(--do-slate-400);
            flex-wrap: wrap;
        }

        .do-sent-badge {
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .do-sent-badge--pos {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .do-sent-badge--neg {
            background: #fee2e2;
            color: #991b1b;
        }

        .do-sent-badge--neu {
            background: var(--do-slate-100);
            color: var(--do-slate-500);
        }

        .do-panel-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            gap: 12px;
            color: var(--do-slate-400);
            font-size: 13px;
            font-weight: 600;
        }

        .do-panel-spinner {
            width: 28px;
            height: 28px;
            border: 2.5px solid var(--do-slate-100);
            border-top-color: var(--do-primary);
            border-radius: 50%;
            animation: spin .65s linear infinite;
        }

        /* Detail sub-panel */
        .do-detail-panel {
            position: absolute;
            inset: 0;
            background: #fff;
            z-index: 5;
            display: none;
            flex-direction: column;
            animation: slideInRight .2s cubic-bezier(.4, 0, .2, 1);
        }

        .do-detail-panel.show {
            display: flex;
        }

        .do-dp2-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            background: var(--do-slate-50);
            border-bottom: 1px solid var(--do-slate-200);
            flex-shrink: 0;
        }

        .do-dp2-back {
            width: 28px;
            height: 28px;
            border-radius: var(--do-radius-sm);
            border: 1px solid var(--do-slate-200);
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--do-slate-500);
            transition: all .13s;
            font-size: 14px;
        }

        .do-dp2-back:hover {
            background: var(--do-primary-lt);
            color: var(--do-primary);
            border-color: var(--do-primary);
        }

        .do-dp2-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--do-slate-900);
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .do-dp2-body {
            overflow-y: auto;
            flex: 1;
            padding: 16px;
        }

        .do-dp2-body::-webkit-scrollbar {
            width: 4px;
        }

        .do-dp2-body::-webkit-scrollbar-thumb {
            background: var(--do-slate-200);
            border-radius: 99px;
        }

        .do-dp2-avatar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .do-dp2-avatar-lg {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--do-slate-200);
            overflow: hidden;
            flex-shrink: 0;
        }

        .do-dp2-avatar-lg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .do-dp2-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--do-slate-900);
        }

        .do-dp2-handle {
            font-size: 11px;
            color: var(--do-slate-400);
            font-weight: 500;
        }

        .do-dp2-plat-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            margin-top: 3px;
        }

        .do-dp2-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: var(--do-slate-400);
            font-weight: 500;
            margin-bottom: 10px;
        }

        .do-dp2-sent {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .do-dp2-sent--pos {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .do-dp2-sent--neg {
            background: #fee2e2;
            color: #991b1b;
        }

        .do-dp2-sent--neu {
            background: var(--do-slate-100);
            color: var(--do-slate-500);
        }

        .do-dp2-content {
            font-size: 12px;
            color: var(--do-slate-700);
            line-height: 1.7;
            margin-bottom: 12px;
            background: var(--do-slate-50);
            border-radius: var(--do-radius-sm);
            padding: 10px 12px;
            border: 1px solid var(--do-slate-200);
            word-break: break-word;
        }

        .do-dp2-media {
            border-radius: var(--do-radius-sm);
            overflow: hidden;
            margin-bottom: 10px;
            background: #000;
        }

        .do-dp2-media img {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            display: block;
        }

        .do-dp2-media--video {
            background: var(--do-slate-900);
        }

        .do-dp2-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 10px;
        }

        .do-dp2-stat {
            background: var(--do-slate-50);
            border-radius: var(--do-radius-sm);
            padding: 8px 10px;
            border: 1px solid var(--do-slate-200);
            text-align: center;
        }

        .do-dp2-stat-val {
            font-size: 14px;
            font-weight: 700;
            color: var(--do-slate-900);
        }

        .do-dp2-stat-lbl {
            font-size: 9px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-top: 1px;
        }

        .do-dp2-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 14px;
            background: var(--do-primary);
            color: #fff;
            border-radius: var(--do-radius-sm);
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: filter .14s;
            margin-top: 4px;
        }

        .do-dp2-link:hover {
            filter: brightness(1.1);
            color: #fff;
        }

        .do-dp2-link i {
            font-size: 13px;
        }

        /* ══ Platform picker ══ */
        .do-plat-picker {
            position: fixed;
            z-index: 20000;
            background: #fff;
            border: 1px solid var(--do-slate-200);
            border-radius: var(--do-radius);
            box-shadow: var(--do-shadow-lg);
            padding: 5px;
            min-width: 175px;
            font-family: inherit;
            display: none;
            animation: fadeUp .14s ease-out;
        }

        .do-plat-picker.show {
            display: block;
        }

        .do-plat-picker-head {
            padding: 4px 9px 6px;
            font-size: 10px;
            font-weight: 700;
            color: var(--do-slate-400);
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid var(--do-slate-100);
            margin-bottom: 3px;
        }

        .do-plat-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border-radius: var(--do-radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            background: transparent;
            border: none;
            font-family: inherit;
            width: 100%;
            text-align: left;
            color: var(--do-slate-700);
            transition: background .12s;
        }

        .do-plat-btn:hover {
            background: var(--do-primary-lt);
            color: var(--do-primary);
        }

        .do-plat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-left: auto;
        }

        /* ══ Modals ══ */
        .do-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 8000;
            background: rgba(15, 23, 42, .55);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
        }

        .do-modal-overlay.show {
            display: flex;
            animation: overlayIn .2s ease-out;
        }

        .do-modal-box {
            background: #fff;
            border-radius: var(--do-radius);
            width: 90%;
            max-width: 560px;
            max-height: 80vh;
            box-shadow: var(--do-shadow-lg);
            overflow: hidden;
            animation: fadeUp .24s ease-out;
            display: flex;
            flex-direction: column;
        }

        .do-modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid var(--do-slate-200);
        }

        .do-modal-head-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--do-slate-900);
            margin: 0;
        }

        .do-modal-head-close {
            width: 30px;
            height: 30px;
            border-radius: var(--do-radius-sm);
            background: #fff;
            border: 1px solid var(--do-slate-200);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .14s;
            font-size: 16px;
            color: var(--do-slate-500);
        }

        .do-modal-head-close:hover {
            background: var(--do-red);
            border-color: var(--do-red);
            color: #fff;
        }

        .do-modal-body {
            padding: 16px 20px 20px;
            overflow-y: auto;
        }

        .do-modal-body::-webkit-scrollbar {
            width: 4px;
        }

        .do-modal-body::-webkit-scrollbar-thumb {
            background: var(--do-slate-200);
            border-radius: 99px;
        }

        /* ══ Export overlay ══ */
        #doExportOverlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .65);
            backdrop-filter: blur(8px);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        #doExportOverlay.show {
            display: flex;
        }

        .do-export-prog-box {
            background: #fff;
            border-radius: var(--do-radius);
            padding: 32px 40px;
            text-align: center;
            box-shadow: var(--do-shadow-lg);
            min-width: 300px;
        }

        .do-export-prog-ring {
            width: 52px;
            height: 52px;
            border: 3px solid var(--do-slate-100);
            border-top-color: var(--do-primary);
            border-radius: 50%;
            animation: spin .7s linear infinite;
            margin: 0 auto 16px;
        }

        .do-export-prog-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--do-slate-900);
            margin-bottom: 5px;
        }

        .do-export-prog-sub {
            font-size: 12px;
            color: var(--do-slate-400);
            font-weight: 500;
        }

        .do-export-prog-bar-wrap {
            height: 5px;
            background: var(--do-slate-100);
            border-radius: 3px;
            margin-top: 16px;
            overflow: hidden;
        }

        .do-export-prog-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--do-primary), #22c55e);
            border-radius: 3px;
            transition: width .4s ease;
        }

        /* ══ Responsive ══ */
        @media(max-width:1280px) {
            .do-row-top {
                grid-template-columns: 1fr 1fr;
            }

            .do-row-top>.card:last-child {
                grid-column: 1/-1;
            }

            .do-row-mid {
                grid-template-columns: 320px 1fr;
            }
        }

        @media(max-width:1024px) {
            .do-row-mid {
                grid-template-columns: 1fr;
            }

            .do-sov-body {
                flex-direction: column;
            }

            .do-sov-chart {
                flex: none;
            }

            .do-sov-legend {
                border-left: none;
                border-top: 1px solid var(--do-slate-200);
            }
        }

        @media(max-width:900px) {
            .do-row-top {
                grid-template-columns: 1fr;
            }

            .do-mention-body {
                flex-direction: column;
            }

            .do-mention-stats {
                width: 100%;
                border-left: none;
                border-top: 1px solid var(--do-slate-200);
                flex-direction: row;
                flex-wrap: wrap;
                gap: 14px;
                padding: 14px 16px;
            }

            .do-map-wrap {
                flex-direction: column;
            }

            .do-loc-panel {
                width: 100%;
                border-left: none;
                border-top: 1px solid var(--do-slate-200);
            }

            .do-panel {
                width: 100vw;
            }
        }
    </style>
@endsection

@section('page-title', 'Data Overview')

@section('content')

    {{-- ══ Export Dropdown (standalone, no header wrapping) ══ --}}
    <!-- <div class="do-export-wrap mb-3 d-flex justify-content-end fade-up">
                <button class="do-export-btn" id="doExportMasterBtn">
                    <i class="ph ph-download-simple"></i> Export Data <i class="ph ph-caret-down"></i>
                </button>
                <div class="do-export-dd" id="doExportDd">
                    <div class="do-export-dd-section">Semua Data</div>
                    <button class="do-export-dd-btn" onclick="DataExporter.exportAll('xlsx')">
                        <i class="ph ph-file-xls"></i> Export ke Excel (.xlsx)
                    </button>
                    <button class="do-export-dd-btn" onclick="DataExporter.exportAll('csv')">
                        <i class="ph ph-file-csv"></i> Export ke CSV
                    </button>
                    <div class="do-export-dd-divider"></div>
                    <div class="do-export-dd-section">Per Platform</div>
                    <button class="do-export-dd-btn" onclick="DataExporter.exportPlatform('doc')">Online News</button>
                    <button class="do-export-dd-btn" onclick="DataExporter.exportPlatform('twit')">X / Twitter</button>
                    <button class="do-export-dd-btn" onclick="DataExporter.exportPlatform('instagram')">Instagram</button>
                </div>
            </div> -->

    {{-- ══ Filter Card ══ --}}
    @include('mk.layouts.partials.filter-datepicker')

    {{-- ══ ROW 1: Trending | Hashtag | Mention ══ --}}
    <div class="do-row-top do-mb14">

        {{-- Trending Topics --}}
        <div class="card fade-up fade-up-d1" data-lazy="trending-topics">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle">
                        <i class="ph ph-trend-up f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Trending Topics</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2" id="trendingHead">
                    <span class="badge bg-light-secondary text-muted rounded-pill">News</span>
                </div>
            </div>
            <div class="card-body do-body-scroll" id="trendingBody">
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;width:70%;"></div>
            </div>
        </div>

        {{-- Top Hashtag --}}
        <div class="card fade-up fade-up-d2" data-lazy="top-hashtags">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle">
                        <i class="ph ph-hash f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Top Hashtag</h6>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2" id="hashtagHead">
                    <span class="badge bg-light-secondary text-muted rounded-pill">X</span>
                </div>
            </div>
            <div class="card-body do-body-scroll" id="hashtagBody">
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;width:70%;"></div>
            </div>
        </div>

        {{-- Mention --}}
        <div class="card fade-up fade-up-d3" data-lazy="mention-combined">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle">
                        <i class="ph ph-chat-dots f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Mention</h6>
                    </div>
                </div>
                <span class="badge bg-light-secondary text-muted rounded-pill">All Media</span>
            </div>
            <div id="mentionSkelWrap" style="padding:16px;">
                <div class="sk-block" style="height:200px;border-radius:6px;"></div>
            </div>
            <div class="do-mention-body" id="mentionBody" style="display:none;">
                <div class="do-mention-chart">
                    <div id="chMentionPie"></div>
                </div>
                <div class="do-mention-stats">
                    <div class="do-mstat-label">Breakdown</div>
                    <div class="do-mstat-row" id="statNewsRow">
                        <span class="do-mstat-name"><span style="background:var(--c-news);"></span>Online News</span>
                        <span class="do-mstat-val" id="mentionNewsVal">—</span>
                    </div>
                    <div class="do-mstat-row" id="statSocialRow">
                        <span class="do-mstat-name"><span style="background:var(--do-primary);"></span>Social Media</span>
                        <span class="do-mstat-val" id="mentionSocialVal">—</span>
                    </div>
                    <div class="do-mstat-divider"></div>
                    <div class="do-mstat-row" id="statTotalRow">
                        <span class="do-mstat-total-lbl">Total</span>
                        <span class="do-mstat-total-val" id="mentionTotalVal">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ ROW 2: SOV | Sentiment Timeline ══ --}}
    <div class="do-row-mid do-mb14">

        {{-- Share of Voice --}}
        <div class="card" data-lazy="sov">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle">
                        <i class="ph ph-microphone f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Share of Voice</h6>
                        <small class="text-muted">Klik untuk lihat mentions per platform</small>
                    </div>
                </div>
                <span class="badge bg-light-secondary text-muted rounded-pill">By Media</span>
            </div>
            <div id="sovSkel" style="padding:16px;">
                <div class="sk-block" style="height:260px;border-radius:6px;"></div>
            </div>
            <div class="do-sov-body" id="sovBody" style="display:none;">
                <div class="do-sov-chart">
                    <div id="chSovPie"></div>
                </div>
                <div class="do-sov-legend">
                    <div class="do-sov-legend-title">Media Platforms</div>
                    <div id="sovLegendItems"></div>
                </div>
            </div>
        </div>

        {{-- Sentiment Timeline --}}
        <div class="card" data-lazy="sentiment-timeline">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle">
                        <i class="ph ph-pulse f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Sentiment Score</h6>
                        <small class="text-muted">Klik pada garis untuk lihat mentions per sentimen</small>
                    </div>
                </div>
                <span class="badge bg-light-secondary text-muted rounded-pill">All Media</span>
            </div>
            <div class="card-body" style="position:relative;height:400px;">
                <div id="chSentiment" style="width:100%;height:100%;"></div>
                <div class="sk-block sk-overlay" id="skSentiment"></div>
            </div>
        </div>
    </div>

    {{-- ══ Buzzer Map ══ --}}
    <div class="card do-mb14" data-lazy="buzzer-map">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle">
                    <i class="ph ph-map-pin f-18 text-primary"></i>
                </div>
                <h6 class="mb-0">Buzzer Map</h6>
            </div>
            <span class="badge bg-light-secondary text-muted rounded-pill">Geographic</span>
        </div>
        <div class="do-map-wrap">
            <div class="do-map-area">
                <div id="buzzMap" style="width:100%;height:400px;"></div>
                <div id="mapSkel" style="position:absolute;inset:0;height:400px;">
                    <div class="sk-block" style="height:100%;border-radius:0;"></div>
                </div>
            </div>
            <div class="do-loc-panel">
                <div class="do-loc-title">Locations</div>
                <div class="do-loc-list" id="buzzMapList">
                    <div style="padding:10px 12px;">
                        <div class="sk-block" style="height:18px;margin-bottom:7px;border-radius:4px;"></div>
                        <div class="sk-block" style="height:18px;margin-bottom:7px;border-radius:4px;"></div>
                        <div class="sk-block" style="height:18px;border-radius:4px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Slide Panel ══ --}}
    <div class="do-panel-overlay" id="doPanelOverlay" onclick="DOPanel.closeByOverlay()"></div>
    <div class="do-panel" id="doSntPanel">
        <div class="do-panel-header" id="doPanelHeader">
            <div class="do-panel-dot" id="doPanelDot"></div>
            <span class="do-panel-title" id="doPanelTitle">Mentions</span>
            <span class="do-panel-count" id="doPanelCount">…</span>
            <button class="do-panel-close" onclick="DOPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-panel-actions">
            <div class="do-panel-meta">
                <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
                <span id="doPanelMeta">—</span>
            </div>
            <div class="do-panel-tabs">
                <button class="do-panel-tab active" data-s="all" onclick="DOPanel.filterSent('all')">Semua</button>
                <button class="do-panel-tab neg" data-s="neg" onclick="DOPanel.filterSent('neg')">Neg</button>
                <button class="do-panel-tab pos" data-s="pos" onclick="DOPanel.filterSent('pos')">Pos</button>
                <button class="do-panel-tab neu" data-s="neu" onclick="DOPanel.filterSent('neu')">Neu</button>
            </div>
            <button class="do-panel-export" onclick="DOPanel.exportCsv()">
                <i class="ph ph-download-simple"></i> CSV
            </button>
        </div>
        <div class="do-panel-list" id="doPanelList"></div>
        <div class="do-detail-panel" id="doDetailPanel">
            <div class="do-dp2-header">
                <button class="do-dp2-back" onclick="DODetail.close()"><i class="ph ph-caret-left"></i></button>
                <span class="do-dp2-title" id="doDetailTitle">Detail</span>
                <button class="do-panel-close" onclick="DOPanel.close()"><i class="ph ph-x"></i></button>
            </div>
            <div class="do-dp2-body" id="doDetailBody"></div>
        </div>
    </div>

    {{-- ══ Platform Picker ══ --}}
    <div class="do-plat-picker" id="doPlatPicker">
        <div class="do-plat-picker-head">Pilih Platform</div>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('twit','all')">X / Twitter <span class="do-plat-dot"
                style="background:var(--c-twitter);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('fb','all')">Facebook <span class="do-plat-dot"
                style="background:var(--c-facebook);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('instagram','all')">Instagram <span class="do-plat-dot"
                style="background:var(--c-instagram);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('youtube','all')">YouTube <span class="do-plat-dot"
                style="background:var(--c-youtube);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('tiktok','all')">TikTok <span class="do-plat-dot"
                style="background:var(--c-tiktok);"></span></button>
    </div>

    {{-- ══ Modals ══ --}}
    <div class="do-modal-overlay" id="doHashtagModal">
        <div class="do-modal-box">
            <div class="do-modal-head">
                <h5 class="do-modal-head-title">Top Hashtags</h5>
                <button class="do-modal-head-close" onclick="DOListModal.close('doHashtagModal')"><i
                        class="ph ph-x"></i></button>
            </div>
            <div class="do-modal-body" id="hashtagModalBody"></div>
        </div>
    </div>
    <div class="do-modal-overlay" id="doTrendingModal">
        <div class="do-modal-box">
            <div class="do-modal-head">
                <h5 class="do-modal-head-title">All Trending Topics</h5>
                <button class="do-modal-head-close" onclick="DOListModal.close('doTrendingModal')"><i
                        class="ph ph-x"></i></button>
            </div>
            <div class="do-modal-body" id="trendingModalBody"></div>
        </div>
    </div>

    {{-- ══ Export overlay ══ --}}
    <div id="doExportOverlay">
        <div class="do-export-prog-box">
            <div class="do-export-prog-ring"></div>
            <div class="do-export-prog-title" id="doExportProgTitle">Mempersiapkan export…</div>
            <div class="do-export-prog-sub" id="doExportProgSub">Mengambil data dari semua platform</div>
            <div class="do-export-prog-bar-wrap">
                <div class="do-export-prog-bar" id="doExportProgBar" style="width:0%"></div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        'use strict';

        /* ══ CONFIG ══ */
        const DOCfg = {
            pid: {{ $projectId ? (int) $projectId : 'null' }},
            sd: '{{ $startDate }}',
            ed: '{{ $endDate }}',
            colorMap: {
                'Online News': '#0284c7',
                'X (Twitter)': '#1d9bf0',
                'Facebook': '#1877f2',
                'Instagram': '#e1306c',
                'YouTube': '#ff0000',
                'TikTok': '#111827',
            },
            platMeta: {
                doc: { label: 'Online News', color: '#0284c7' },
                twit: { label: 'X / Twitter', color: '#1d9bf0' },
                fb: { label: 'Facebook', color: '#1877f2' },
                instagram: { label: 'Instagram', color: '#e1306c' },
                youtube: { label: 'YouTube', color: '#ff0000' },
                tiktok: { label: 'TikTok', color: '#111827' },
                all: { label: 'All Media', color: '#4361EE' },
                social: { label: 'Social Media', color: '#4361EE' },
            },
            mediaKeyMap: {
                'Online News': 'doc',
                'X (Twitter)': 'twit',
                'Facebook': 'fb',
                'Instagram': 'instagram',
                'YouTube': 'youtube',
                'TikTok': 'tiktok',
            }
        };

        /* ── Helpers ── */
        const $ = id => document.getElementById(id);
        const numFmt = n => parseInt(n || 0).toLocaleString('id-ID');
        const numK = n => { n = parseInt(n || 0); return n >= 1e6 ? (n / 1e6).toFixed(1) + 'M' : n >= 1000 ? (n / 1000).toFixed(1) + 'k' : String(n); };
        const esc = s => (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const emptyHtml = m => `<div class="do-empty"><i class="ph ph-warning-circle"></i><span class="do-empty-txt">${m || 'Tidak ada data'}</span></div>`;

        /* ── ECharts ── */
        const DOCharts = {
            _inst: {},
            make(id) {
                if (this._inst[id]) { try { this._inst[id].dispose(); } catch (e) { } }
                const dom = $(id); if (!dom) return null;
                const c = echarts.init(dom, null, { renderer: 'canvas' });
                this._inst[id] = c; return c;
            }
        };
        window.addEventListener('resize', () => Object.values(DOCharts._inst).forEach(c => { try { if (!c.isDisposed()) c.resize(); } catch (e) { } }));

        const EC_TT = {
            backgroundColor: '#1e293b', borderColor: '#334155', borderWidth: 1,
            padding: [9, 13], textStyle: { color: '#fff', fontFamily: 'inherit', fontSize: 12 },
            extraCssText: 'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
        };

        function getPrimary() { return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#4361EE'; }

        /* ══ LIST MODALS ══ */
        const DOListModal = {
            open(id) { $(id).classList.add('show'); document.body.style.overflow = 'hidden'; },
            close(id) { $(id).classList.remove('show'); document.body.style.overflow = 'auto'; },
            openTrending(topics) {
                let h = `<table class="do-tbl"><thead><tr><th style="width:30px;">#</th><th>Topic</th></tr></thead><tbody>`;
                topics.forEach((t, i) => {
                    const name = t.title || t.name || t.topic || 'Unknown', url = t.reference || t.url || '#';
                    h += `<tr><td class="do-tbl-rank">${i + 1}</td><td class="do-tbl-name">${url !== '#' ? `<a href="${url}" target="_blank" class="topic-link">${esc(name)}</a>` : esc(name)}</td></tr>`;
                });
                h += '</tbody></table>';
                $('trendingModalBody').innerHTML = h; this.open('doTrendingModal');
            },
            openHashtag(tags) {
                let h = `<table class="do-tbl"><thead><tr><th style="width:30px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
                tags.forEach((tag, i) => {
                    let name = tag.name || tag.hashtag || tag.tag || '?'; if (!name.startsWith('#')) name = '#' + name;
                    h += `<tr><td class="do-tbl-rank">${i + 1}</td><td class="do-tbl-name" style="color:var(--do-primary);font-weight:700;">${name}</td><td class="do-tbl-num">${parseInt(tag.size || tag.mention || tag.count || 0).toLocaleString()}</td></tr>`;
                });
                h += '</tbody></table>';
                $('hashtagModalBody').innerHTML = h; this.open('doHashtagModal');
            }
        };
        window.addEventListener('click', e => {
            if (e.target === $('doHashtagModal')) DOListModal.close('doHashtagModal');
            if (e.target === $('doTrendingModal')) DOListModal.close('doTrendingModal');
        });

        /* ══ SLIDE PANEL ══ */
        const DOPanel = (() => {
            let _cache = {}, _allItems = [], _filtered = [], _curSent = 'all', _curPlat = null, _curPlatForSent = 'all';
            const SENT_MAP = { '1': 'pos', 'positive': 'pos', 'positif': 'pos', '-1': 'neg', '2': 'neg', 'negative': 'neg', 'negatif': 'neg' };
            function _normSent(item) { const r = String(item.class_sentiment || item.sentiment || '0').toLowerCase().trim(); return SENT_MAP[r] || 'neu'; }

            function showPlatPicker(x, y, sent) {
                _curPlatForSent = sent || 'all';
                const pp = $('doPlatPicker'); if (!pp) return;
                pp.querySelectorAll('.do-plat-btn').forEach(btn => {
                    const m = btn.getAttribute('onclick') || '';
                    const pm = m.match(/openPlatform\('([^']+)'/);
                    if (pm) btn.setAttribute('onclick', `DOPanel.openPlatform('${pm[1]}','${_curPlatForSent}')`);
                });
                const pw = 180, ph = 250, vw = window.innerWidth, vh = window.innerHeight;
                let left = x + 10, top = y - 10;
                if (left + pw > vw - 8) left = x - pw - 10; if (top + ph > vh - 8) top = vh - ph - 8; if (top < 8) top = 8;
                pp.style.left = left + 'px'; pp.style.top = top + 'px'; pp.classList.add('show');
            }
            function openPlatform(platform, sentiment) {
                $('doPlatPicker')?.classList.remove('show');
                open(platform, sentiment || _curPlatForSent || 'all');
            }

            async function open(platform, sentiment) {
                _curPlat = platform; _curSent = sentiment || 'all';
                const meta = DOCfg.platMeta[platform] || { label: platform, color: '#4361EE' };
                DODetail.close();
                $('doPanelDot').style.background = meta.color;
                $('doPanelTitle').textContent = meta.label;
                $('doPanelCount').textContent = '…';
                $('doPanelMeta').textContent = DOCfg.sd + ' – ' + DOCfg.ed;
                document.querySelectorAll('.do-panel-tab').forEach(t => t.classList.toggle('active', t.dataset.s === _curSent));
                const list = $('doPanelList');
                list.innerHTML = `<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
                const overlay = $('doPanelOverlay'), panel = $('doSntPanel');
                overlay.classList.remove('hiding'); panel.classList.remove('hiding');
                overlay.classList.add('show'); panel.classList.add('show');
                try {
                    const key = `${DOCfg.pid}_${platform}_${DOCfg.sd}_${DOCfg.ed}`;
                    if (!_cache[key]) _cache[key] = await _fetchAll(platform);
                    _allItems = _cache[key];
                    _filtered = _filterBySent(_allItems, _curSent);
                    $('doPanelCount').textContent = _filtered.length.toLocaleString();
                    _render(list, _filtered, platform, meta.color);
                } catch (err) {
                    list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:13px;">Gagal memuat data<br><small>${esc(err.message)}</small></div>`;
                    $('doPanelCount').textContent = '0';
                }
            }

            function close() {
                const overlay = $('doPanelOverlay'), panel = $('doSntPanel');
                panel.classList.add('hiding'); overlay.classList.add('hiding');
                setTimeout(() => {
                    panel.classList.remove('show', 'hiding');
                    overlay.classList.remove('show', 'hiding');
                    DODetail.close();
                }, 240);
            }
            function closeByOverlay() { close(); }

            function filterSent(sent) {
                _curSent = sent;
                document.querySelectorAll('.do-panel-tab').forEach(t => t.classList.toggle('active', t.dataset.s === sent));
                _filtered = _filterBySent(_allItems, sent);
                $('doPanelCount').textContent = _filtered.length.toLocaleString();
                const meta = DOCfg.platMeta[_curPlat] || { label: _curPlat, color: '#4361EE' };
                _render($('doPanelList'), _filtered, _curPlat, meta.color);
            }

            function _filterBySent(items, sent) { return sent === 'all' ? items : items.filter(i => _normSent(i) === sent); }

            function exportCsv() {
                if (!_filtered.length) { alert('Tidak ada data.'); return; }
                const rows = _filtered.map(item => ({
                    nama: (item.author_name || item.channel_name || item.from_name || item.publisher || item.source_name || '').trim(),
                    sentimen: { pos: 'Positif', neg: 'Negatif', neu: 'Netral' }[_normSent(item)],
                    tanggal: item.date_created || '', url: item.url || item.link || '',
                    konten: (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim().slice(0, 500),
                }));
                const meta = DOCfg.platMeta[_curPlat] || { label: _curPlat };
                DataExporter.downloadCSV(rows, `sentiment_${meta.label.replace(/\s+/g, '_')}_${_curSent}_${DOCfg.sd}_${DOCfg.ed}`);
            }

            async function _fetchAll(platform) {
                if (platform === 'all') {
                    const all = ['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok'];
                    const res = await Promise.allSettled(all.map(p => _fetchOne(p)));
                    return res.flatMap(r => r.status === 'fulfilled' ? r.value : []);
                }
                if (platform === 'social') {
                    const socials = ['twit', 'fb', 'instagram', 'youtube', 'tiktok'];
                    const res = await Promise.allSettled(socials.map(p => _fetchOne(p)));
                    return res.flatMap(r => r.status === 'fulfilled' ? r.value : []);
                }
                return _fetchOne(platform);
            }

            async function _fetchOne(platform) {
                const q = `project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}&rows=500&start=0`;
                if (platform === 'instagram') {
                    for (const sub of ['postbylike', 'postbycomment', 'postbydate', '']) {
                        try { const r = await fetch(`/mk/api/news/ig-top-status?${q}${sub ? '&sub=' + sub : ''}`); const d = await r.json(); const items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []); if (items.length > 0) return items.map(i => ({ ...i, _platform: platform })); } catch (e) { continue; }
                    } return [];
                }
                const eps = {
                    doc: `/mk/api/news/mentions?${q}`,
                    twit: `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
                    fb: `/mk/api/news/fb-top-status?${q}&sub=fblike`,
                    youtube: `/mk/api/news/ytb-top-status?${q}`,
                    tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
                };
                const twitFallback = `/mk/api/news/mentions?${q}&media_type=twit`;
                const url = eps[platform]; if (!url) return [];
                const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 30000);
                try {
                    const r = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
                    if (!r.ok) { return []; }
                    const d = await r.json();
                    let items = [];
                    if (Array.isArray(d?.data?.data)) items = d.data.data;
                    else if (Array.isArray(d?.data)) items = d.data;
                    else if (Array.isArray(d?.statuses)) items = d.statuses;
                    else if (Array.isArray(d?.tweets)) items = d.tweets;
                    else if (Array.isArray(d?.results)) items = d.results;
                    else if (Array.isArray(d?.posts)) items = d.posts;
                    else if (Array.isArray(d)) items = d;
                    else if (d?.data && typeof d.data === 'object' && !Array.isArray(d.data)) {
                        const vals = Object.values(d.data);
                        if (vals.length && typeof vals[0] === 'object') items = vals;
                    }
                    if (platform === 'twit' && items.length === 0) {
                        try {
                            const r2 = await fetch(twitFallback);
                            const d2 = await r2.json();
                            let fb = [];
                            if (Array.isArray(d2?.data?.data)) fb = d2.data.data;
                            else if (Array.isArray(d2?.data)) fb = d2.data;
                            else if (Array.isArray(d2)) fb = d2;
                            items = fb.filter(m => String(m.media_type || '').toLowerCase() === 'twit' || String(m.tcode || '').toLowerCase() === 'rt' || String(m.tcode || '').toLowerCase() === 'twit');
                        } catch (e2) { }
                    }
                    if (platform === 'doc') items = items.filter(m => { const tc = String(m.tcode || '').toLowerCase(), mt = String(m.media_type || '').toLowerCase(); return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online' || mt === 'article'; });
                    return items.map(i => ({ ...i, _platform: platform }));
                } catch (e) { clearTimeout(tid); return []; }
            }

            function _render(list, items, platform, accentColor) {
                if (!items.length) { list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`; return; }
                const SHOW = 60;
                list.innerHTML = items.slice(0, SHOW).map(item => {
                    const plat = item._platform || platform;
                    const meta = DOCfg.platMeta[plat] || { label: plat, color: accentColor };
                    const rawName = (() => {
                        if (plat === 'fb') return item.from_name || item.page_name || null;
                        if (plat === 'instagram') return item.username || item.user_name || null;
                        if (plat === 'tiktok') return item.author_nickname || item.nickname || item.author?.nickname || null;
                        if (plat === 'youtube') return item.channel_title || item.channel_name || item.snippet?.channelTitle || null;
                        if (plat === 'twit') {
                            const authorObj = typeof item.author === 'object' ? item.author : (() => { try { return JSON.parse(item.author || '{}'); } catch (e) { return {}; } })();
                            return item.name || authorObj?.name || authorObj?.scr_name || item.author_name || null;
                        }
                        return null;
                    })();
                    const name = (rawName || item.author_name || item.channel_name || item.publisher || item.source_name || 'Unknown').trim();
                    const isNum = /^\d{10,}$/.test(name); const dName = isNum ? `User ${name.slice(-4)}` : name;
                    const rawH = (() => {
                        if (plat === 'instagram') return item.username || '';
                        if (plat === 'twit') {
                            const authorObj = typeof item.author === 'object' ? item.author : (() => { try { return JSON.parse(item.author || '{}'); } catch (e) { return {}; } })();
                            return item.screen_name || item.author_scr_name || authorObj?.scr_name || authorObj?.username || '';
                        }
                        return item.author_scr_name || item.screen_name || item.username || '';
                    })().trim();
                    const handle = (() => { if (!rawH) return ''; const w = ['twit', 'instagram', 'tiktok'].includes(plat) ? (rawH.startsWith('@') ? rawH : '@' + rawH) : rawH; return w.replace(/^@/, '').toLowerCase() === dName.toLowerCase() ? '' : w; })();
                    const text = (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim().slice(0, 150);
                    const _authorObj = (() => { if (typeof item.author === 'object' && item.author) return item.author; try { return JSON.parse(item.author || '{}'); } catch (e) { return {}; } })();
                    const av = (item.avatar_url || item.profile_image_url || _authorObj?.image || item.author_image || item.profile_image || item.thumbnail || '').trim();
                    const dt = (item.date_created || item.created_at || '').split('T')[0];
                    const sent = _normSent(item);
                    const sentLbl = sent === 'pos' ? 'Pos' : sent === 'neg' ? 'Neg' : 'Neu';
                    const words = dName.replace(/[^a-zA-Z0-9\s]/g, '').trim().split(/\s+/).filter(Boolean);
                    const ini = (words.length >= 2 ? (words[0][0] + words[words.length - 1][0]) : (words[0]?.[0] || dName[0] || '?')).toUpperCase();
                    const safeIni = ini.replace(/['"]/g, '');
                    const avHtml = (av && av.startsWith('http')) ? `<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}';">` : ini;
                    const sentBadge = `do-sent-badge--${sent === 'pos' ? 'pos' : sent === 'neg' ? 'neg' : 'neu'}`;
                    const enc = encodeURIComponent(JSON.stringify(item));
                    return `<div class="do-panel-item" onclick="DODetail.openEncoded('${enc}','${plat}')">
                            <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                            <div class="do-panel-item-body">
                                <div class="do-panel-author">${esc(dName)}</div>
                                ${handle ? `<div class="do-panel-handle">${esc(handle)}</div>` : ''}
                                <div class="do-panel-text">${esc(text || '(tidak ada konten)')}</div>
                                <div class="do-panel-footer">
                                    <span class="do-sent-badge ${sentBadge}">${sentLbl}</span>
                                    <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                                    <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                                    ${dt ? `<span style="margin-left:auto;">${dt}</span>` : ''}
                                </div>
                            </div>
                        </div>`;
                }).join('');
                if (items.length > SHOW) list.insertAdjacentHTML('beforeend', `<div style="padding:9px;text-align:center;font-size:11px;font-weight:600;color:var(--do-slate-400);background:var(--do-slate-50);border-top:1px dashed var(--do-slate-200);">+${(items.length - SHOW).toLocaleString()} mentions lainnya · Export CSV untuk lihat semua</div>`);
            }

            return { open, close, closeByOverlay, showPlatPicker, openPlatform, filterSent, exportCsv };
        })();

        /* ══ DETAIL SUB-PANEL ══ */
        const DODetail = {
            openEncoded(enc, plat) { try { this.open(JSON.parse(decodeURIComponent(enc)), plat); } catch (e) { } },
            open(item, platform) {
                const panel = $('doDetailPanel'), body = $('doDetailBody'), title = $('doDetailTitle');
                if (!panel || !body) return;
                const meta = DOCfg.platMeta[platform] || { label: platform, color: '#4361EE' };
                const SENT_MAP = { pos: 'Positif', neg: 'Negatif', neu: 'Netral' };
                const SENT_BGS = { pos: 'do-dp2-sent--pos', neg: 'do-dp2-sent--neg', neu: 'do-dp2-sent--neu' };
                const raw = String(item.class_sentiment || item.sentiment || '0').toLowerCase();
                const sent = { '1': 'pos', 'positive': 'pos', 'positif': 'pos', '-1': 'neg', '2': 'neg', 'negative': 'neg', 'negatif': 'neg' }[raw] || 'neu';
                const rawName = (() => { if (platform === 'fb') return item.from_name || item.page_name || null; if (platform === 'instagram') return item.username || null; if (platform === 'tiktok') return item.author_nickname || item.nickname || item.author?.nickname || null; if (platform === 'youtube') return item.channel_title || item.channel_name || item.snippet?.channelTitle || null; if (platform === 'twit') return item.name || item.user?.name || item.author_name || null; return null; })();
                const name = (rawName || item.author_name || item.channel_name || item.publisher || item.source_name || 'Unknown').trim();
                const handle = ((platform === 'instagram' ? item.username : '') || item.author_scr_name || item.screen_name || item.username || '').trim();
                const content = (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim();
                const av = (item.avatar_url || item.profile_image_url || item.author_image || item.profile_image || item.thumbnail || '').trim();
                const url = item.url || item.link || '';
                const dt = item.date_created || item.created_at || '';
                title.textContent = name;
                const words = name.replace(/[^a-zA-Z0-9\s]/g, '').trim().split(/\s+/).filter(Boolean);
                const ini = (words.length >= 2 ? (words[0][0] + words[words.length - 1][0]) : (words[0]?.[0] || name[0] || '?')).toUpperCase();
                const avHtml = (av && av.startsWith('http')) ? `<img src="${esc(av)}" onerror="this.parentElement.textContent='${ini}';">` : ini;
                let dtFmt = ''; if (dt) { try { dtFmt = new Date(dt).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }); } catch (e) { dtFmt = dt.split('T')[0]; } }

                let mediaHtml = '';
                if (platform === 'youtube') {
                    const ytId = (url.match(/[?&]v=([a-zA-Z0-9_-]{11})/) || url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/) || url.match(/embed\/([a-zA-Z0-9_-]{11})/) || url.match(/shorts\/([a-zA-Z0-9_-]{11})/) || [])[1] || (item.video_id || item.youtube_id || '');
                    const thumb = item.thumbnail || item.thumbnail_url || item.image_url || item.media_url || (ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : '');
                    if (ytId) {
                        const embedId = `yt_${ytId}_${Date.now()}`;
                        mediaHtml = `<div class="do-dp2-media do-dp2-media--video" id="${embedId}" style="position:relative;cursor:pointer;border-radius:6px;overflow:hidden;background:#000;" onclick="document.getElementById('${embedId}').innerHTML='<iframe width=\\\"100%\\\" height=\\\"280\\\" src=\\\"https://www.youtube.com/embed/${ytId}?autoplay=1&controls=1\\\" frameborder=\\\"0\\\" allow=\\\"accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture\\\" allowfullscreen></iframe>'">
                                <img src="${thumb || `https://img.youtube.com/vi/${ytId}/hqdefault.jpg`}" style="width:100%;height:220px;object-fit:cover;display:block;" onerror="this.src='https://img.youtube.com/vi/${ytId}/mqdefault.jpg'">
                                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.25);">
                                    <div style="width:52px;height:52px;background:#ff0000;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.4);">
                                        <i class="ph ph-play-fill" style="font-size:22px;color:#fff;margin-left:3px;"></i>
                                    </div>
                                </div>
                            </div>`;
                    } else if (thumb) {
                        mediaHtml = `<div class="do-dp2-media"><img src="${esc(thumb)}" onerror="this.parentElement.style.display='none'" style="border-radius:6px;width:100%;max-height:220px;object-fit:cover;"></div>`;
                    }
                } else if (platform === 'tiktok') {
                    const tid = (url.match(/\/video\/(\d+)/) || url.match(/\/v\/(\d+)/) || [])[1] || (item.video_id || item.aweme_id || '');
                    const thumb = item.thumbnail || item.cover || item.image_url || item.video_cover || item.media_url || '';
                    if (tid) {
                        const embedId = `tt_${tid}_${Date.now()}`;
                        mediaHtml = `<div id="${embedId}" style="position:relative;cursor:pointer;background:#111827;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;height:260px;" onclick="DODetail.loadTikTokEmbed('${embedId}','${tid}')">
                                ${thumb ? `<img src="${esc(thumb)}" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:.65;pointer-events:none;">` : ''}
                                <div style="position:relative;z-index:2;width:56px;height:56px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.6);">
                                    <i class="ph ph-play-fill" style="font-size:24px;color:#111827;margin-left:3px;"></i>
                                </div>
                                <div style="position:absolute;bottom:8px;right:8px;background:#111827;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:3px;letter-spacing:.5px;">TIKTOK</div>
                            </div>`;
                    } else if (thumb) {
                        mediaHtml = `<div class="do-dp2-media"><img src="${esc(thumb)}" onerror="this.parentElement.style.display='none'" style="border-radius:6px;max-height:320px;object-fit:cover;width:100%;display:block;"></div>`;
                    }
                } else if (platform === 'instagram') {
                    const thumb = item.image_url || item.thumbnail || item.media_url || item.picture || item.display_url || '';
                    const isVideo = (item.media_type || '').toLowerCase() === 'video' || (item.product_type || '').toLowerCase() === 'igtv' || (item.product_type || '').toLowerCase() === 'reels';
                    if (thumb) {
                        mediaHtml = `<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;">
                                <img src="${esc(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:320px;object-fit:cover;display:block;">
                                ${isVideo ? `<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#e1306c;margin-left:3px;"></i></div></div>` : ''}
                            </div>`;
                    }
                } else if (platform === 'fb') {
                    const imgUrl = item.image_url || item.thumbnail || item.media_url || item.picture || item.display_url || item.story_img || '';
                    const isVideo = (item.type || '').includes('video') || (item.video_id) ? true : false;
                    if (imgUrl) {
                        mediaHtml = `<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;">
                                <img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:320px;object-fit:cover;display:block;">
                                ${isVideo ? `<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#1877f2;margin-left:3px;"></i></div></div>` : ''}
                            </div>`;
                    }
                } else if (platform === 'twit') {
                    const imgUrl = item.image_url || item.media_url || item.thumbnail || item.display_url || item.media?.media_url || '';
                    const isVideo = String(item.media_type || '').toLowerCase() === 'video' || String(item.type || '').toLowerCase() === 'video';
                    if (imgUrl) {
                        mediaHtml = `<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;">
                                <img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:320px;object-fit:cover;display:block;">
                                ${isVideo ? `<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#1d9bf0;margin-left:3px;"></i></div></div>` : ''}
                            </div>`;
                    }
                } else {
                    const imgUrl = item.image_url || item.thumbnail || item.featured_image || item.banner_image || item.media_url || item.picture || '';
                    if (imgUrl) mediaHtml = `<div class="do-dp2-media" style="border-radius:6px;overflow:hidden;background:#e5e7eb;"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:260px;object-fit:cover;display:block;"></div>`;
                }

                const statsMap = { twit: [['Retweet', item.num_retweeted || item.retweet_count || 0], ['Like', item.num_likes || item.favorite_count || 0], ['Quote', item.num_quote || 0]], fb: [['Like', item.likes || item.num_likes || 0], ['Share', item.shares || item.share_count || 0], ['Comment', item.num_comments || 0]], instagram: [['Like', item.num_likes || item.likes || 0], ['Comment', item.num_comments || item.comment_count || 0], ['View', item.num_views || item.views || 0]], youtube: [['View', item.num_views || item.views || 0], ['Like', item.num_likes || item.likes || 0], ['Comment', item.num_comments || 0]], tiktok: [['Play', item.views || item.play_count || 0], ['Like', item.likes || item.digg_count || 0], ['Share', item.shares || item.share_count || 0]], doc: [['Read', item.num_views || 0], ['Share', item.num_share || 0], ['Comment', item.num_comments || 0]] };
                const stats = statsMap[platform] || [];
                const statsHtml = stats.some(s => parseInt(s[1]) > 0) ? `<div class="do-dp2-stats">${stats.map(([l, v]) => `<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v || 0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`).join('')}</div>` : '';
                const handleDisp = handle && !handle.replace('@', '').toLowerCase().startsWith(name.toLowerCase().slice(0, 4)) ? (handle.startsWith('@') ? handle : '@' + handle) : '';
                body.innerHTML = `
                        <div class="do-dp2-avatar-row">
                            <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                            <div>
                                <div class="do-dp2-name">${esc(name)}</div>
                                ${handleDisp ? `<div class="do-dp2-handle">${esc(handleDisp)}</div>` : ''}
                                <span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span>
                            </div>
                        </div>
                        ${dtFmt ? `<div class="do-dp2-meta"><span>${dtFmt}</span></div>` : ''}
                        <div class="do-dp2-sent ${SENT_BGS[sent]}">${SENT_MAP[sent]}</div>
                        ${mediaHtml}
                        ${content ? `<div class="do-dp2-content">${esc(content)}</div>` : ''}
                        ${statsHtml}
                        ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i> Lihat ${meta.label} Asli</a>` : ''}`;
                panel.classList.add('show');
            },
            close() {
                $('doDetailPanel')?.classList.remove('show');
                document.querySelectorAll('.do-detail-panel iframe').forEach(iframe => { iframe.src = iframe.src; });
            },
            loadTikTokEmbed(embedId, videoIdOrUrl) {
                const el = $(embedId); if (!el) return;
                let tid = '';
                if (/^\d+$/.test(videoIdOrUrl)) { tid = videoIdOrUrl; }
                else { tid = (videoIdOrUrl.match(/\/video\/(\d+)/) || videoIdOrUrl.match(/\/v\/(\d+)/) || [])[1] || ''; }
                if (!tid) { window.open(videoIdOrUrl, '_blank'); return; }
                el.style.cursor = 'default'; el.style.minHeight = '560px'; el.style.height = 'auto';
                el.style.background = '#111827'; el.style.borderRadius = '6px'; el.style.overflow = 'hidden';
                el.innerHTML = `<iframe src="https://www.tiktok.com/embed/v2/${tid}" width="100%" height="560" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="display:block;border:none;border-radius:6px;background:#111827;"></iframe>`;
            }
        };

        /* ══ LAZY LOADER ══ */
        const DOLoader = {
            loaded: new Set(),
            init() {
                const obs = new IntersectionObserver(entries => {
                    entries.forEach(e => {
                        if (e.isIntersecting) { const card = e.target, sec = card.dataset.lazy; if (!this.loaded.has(sec)) { this.loaded.add(sec); this.load(sec, card); obs.unobserve(card); } }
                    });
                }, { rootMargin: '100px', threshold: .05 });
                document.querySelectorAll('[data-lazy]').forEach(c => obs.observe(c));
            },
            async load(sec, card) {
                try {
                    switch (sec) {
                        case 'trending-topics': await this.loadTrending(); break;
                        case 'top-hashtags': await this.loadHashtags(); break;
                        case 'mention-combined': await this.loadMentions(); break;
                        case 'sov': await this.loadSov(); break;
                        case 'sentiment-timeline': await this.loadSentLine(); break;
                        case 'buzzer-map': await this.loadMap(); break;
                    }
                } catch (err) { console.error(`Error loading ${sec}:`, err); }
            },

            async loadTrending() {
                const r = await fetch(`/mk/api/trending-topics`); const d = await r.json();
                const body = $('trendingBody'), topics = d.data || [];
                if (!topics.length) { body.innerHTML = emptyHtml(); return; }
                if (topics.length > 10) $('trendingHead').insertAdjacentHTML('beforeend', `<button class="do-view-all" onclick="DOListModal.openTrending(window._doTopics)"><i class="ph ph-caret-right"></i>All</button>`);
                window._doTopics = topics;
                let h = `<table class="do-tbl"><thead><tr><th style="width:22px;">#</th><th>Topic</th></tr></thead><tbody>`;
                topics.slice(0, 10).forEach((t, i) => {
                    const name = t.title || t.name || t.topic || 'Unknown', url = t.reference || t.url || '#';
                    h += `<tr><td class="do-tbl-rank">${i + 1}</td><td class="do-tbl-name">${url !== '#' ? `<a href="${url}" target="_blank" class="topic-link">${esc(name)}</a>` : esc(name)}</td></tr>`;
                });
                h += '</tbody></table>'; body.innerHTML = h;
            },

            async loadHashtags() {
                const r = await fetch(`/mk/api/top-hashtags?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d = await r.json();
                const body = $('hashtagBody');
                let tags = d.data && Array.isArray(d.data.hashtags) ? d.data.hashtags : (Array.isArray(d.data) ? d.data : []);
                if (!tags.length) { body.innerHTML = emptyHtml(); return; }
                if (tags.length > 5) $('hashtagHead').insertAdjacentHTML('beforeend', `<button class="do-view-all" onclick="DOListModal.openHashtag(window._doHashtags)"><i class="ph ph-caret-right"></i>All</button>`);
                window._doHashtags = tags;
                let h = `<table class="do-tbl"><thead><tr><th style="width:22px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
                tags.slice(0, 5).forEach((tag, i) => {
                    let name = tag.name || tag.hashtag || tag.tag || '?'; if (!name.startsWith('#')) name = '#' + name;
                    h += `<tr><td class="do-tbl-rank">${i + 1}</td><td class="do-tbl-name" style="color:var(--do-primary);font-weight:700;">${name}</td><td class="do-tbl-num">${parseInt(tag.size || tag.mention || tag.count || 0).toLocaleString()}</td></tr>`;
                });
                h += '</tbody></table>'; body.innerHTML = h;
            },

            async loadMentions() {
                const r = await fetch(`/mk/api/mention-counts?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d = await r.json();
                const social = Number(d.social || 0), news = Number(d.news || 0), total = social + news;
                $('mentionNewsVal').textContent = numFmt(news);
                $('mentionSocialVal').textContent = numFmt(social);
                $('mentionTotalVal').textContent = numFmt(total);
                $('mentionSkelWrap').style.display = 'none';
                $('mentionBody').style.display = 'flex';
                $('statNewsRow').onclick = () => DOPanel.open('doc', 'all');
                $('statSocialRow').onclick = (e) => DOPanel.showPlatPicker(e.clientX, e.clientY, 'all');
                $('statTotalRow').onclick = () => DOPanel.open('all', 'all');
                if (total > 0) {
                    const chart = DOCharts.make('chMentionPie');
                    if (chart) {
                        const primary = getPrimary();
                        chart.setOption({
                            animation: true, animationDuration: 800, animationEasing: 'cubicInOut',
                            tooltip: { ...EC_TT, trigger: 'item', confine: true, formatter: p => { const pct = total > 0 ? p.value / total * 100 : 0; const dot = `<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};margin-right:6px;flex-shrink:0;"></span>`; return `<div style="display:flex;align-items:center;font-weight:700;margin-bottom:4px;">${dot}${p.name}</div><div style="padding-left:14px;">${numFmt(p.value)} mentions</div><div style="padding-left:14px;opacity:.7;">${pct < 1 && pct > 0 ? '<1' : Math.round(pct)}% dari total</div>`; } },
                            legend: { show: true, bottom: 0, orient: 'horizontal', textStyle: { fontFamily: 'inherit', fontSize: 10, fontWeight: '600', color: 'var(--do-slate-400)' }, icon: 'circle', itemWidth: 7, itemHeight: 7, itemGap: 10 },
                            series: [{
                                type: 'pie', radius: ['48%', '72%'], center: ['50%', '45%'], avoidLabelOverlap: true,
                                itemStyle: { borderRadius: 4, borderColor: '#fff', borderWidth: 2 }, label: { show: false },
                                emphasis: { label: { show: true, fontSize: 11, fontWeight: '700', fontFamily: 'inherit', formatter: p => { const pct = total > 0 ? p.value / total * 100 : 0; return `{n|${p.name}}\n{v|${numK(p.value)}}\n{p|${pct < 1 && pct > 0 ? '<1%' : Math.round(pct) + '%'}}`; }, rich: { n: { fontSize: 9, color: 'var(--do-slate-400)', fontWeight: '600', lineHeight: 14 }, v: { fontSize: 13, color: 'var(--do-slate-900)', fontWeight: '700', lineHeight: 18 }, p: { fontSize: 9, color: primary, fontWeight: '700', lineHeight: 14 } } }, scale: true, scaleSize: 4 },
                                data: [{ name: 'Online News', value: news, itemStyle: { color: '#0284c7' } }, { name: 'Social Media', value: social, itemStyle: { color: primary } }],
                            }]
                        });
                        chart.on('click', p => { if (p.name === 'Online News') DOPanel.open('doc', 'all'); else DOPanel.showPlatPicker(window.innerWidth / 2, window.innerHeight / 2, 'all'); });
                        chart.on('mouseover', p => { if (p.componentType === 'series') chart.getDom().style.cursor = 'pointer'; });
                        chart.on('mouseout', () => { chart.getDom().style.cursor = 'default'; });
                    }
                }
            },

            async loadSov() {
                const r = await fetch(`/mk/api/sentiment-by-media?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d = await r.json();
                const data = d.data || [];
                $('sovSkel').style.display = 'none';
                const sovBody = $('sovBody'); if (sovBody) sovBody.style.display = 'flex';
                if (!data.length) { if (sovBody) sovBody.innerHTML = emptyHtml(); return; }
                const fallback = ['#22c55e', '#1d9bf0', '#1877f2', '#e1306c', '#ff0000', '#111827', '#7c3aed', '#f59e0b'];
                const totalAll = data.reduce((s, m) => s + (m.total || 0), 0);
                const labels = data.map(m => m.media), counts = data.map(m => m.total || 0), colors = data.map((m, i) => DOCfg.colorMap[m.media] || fallback[i % fallback.length]);
                const primary = getPrimary();
                const chart = DOCharts.make('chSovPie');
                if (chart) {
                    chart.setOption({
                        animation: true, animationDuration: 800, animationEasing: 'cubicInOut',
                        tooltip: { ...EC_TT, trigger: 'item', confine: true, formatter: p => { const pct = totalAll > 0 ? p.value / totalAll * 100 : 0; return `<div style="font-weight:700;margin-bottom:3px;">${p.name}</div><div>${numFmt(p.value)} mentions</div><div style="opacity:.7;">${pct < 1 && pct > 0 ? '<1' : pct.toFixed(1)}% dari total</div>`; } },
                        legend: { show: false },
                        series: [{
                            type: 'pie', radius: ['48%', '76%'], center: ['50%', '50%'], avoidLabelOverlap: true, itemStyle: { borderRadius: 5, borderColor: '#fff', borderWidth: 2.5 }, label: { show: false },
                            emphasis: { label: { show: true, fontSize: 12, fontWeight: '700', fontFamily: 'inherit', formatter: p => { const pct = totalAll > 0 ? p.value / totalAll * 100 : 0; return `{n|${p.name}}\n{v|${numK(p.value)}}\n{p|${pct < 1 && pct > 0 ? '<1%' : pct.toFixed(1) + '%'}}`; }, rich: { n: { fontSize: 9, color: 'var(--do-slate-400)', fontWeight: '600', lineHeight: 14 }, v: { fontSize: 14, color: 'var(--do-slate-900)', fontWeight: '700', lineHeight: 20 }, p: { fontSize: 9, color: primary, fontWeight: '800', lineHeight: 14 } } }, scale: true, scaleSize: 4 },
                            data: labels.map((lb, i) => ({ name: lb, value: counts[i], itemStyle: { color: colors[i] } }))
                        }]
                    });
                    chart.on('click', p => { const k = DOCfg.mediaKeyMap[p.name]; if (!k) return; DOPanel.open(k, 'all'); });
                    chart.on('mouseover', p => { if (p.componentType === 'series') chart.getDom().style.cursor = 'pointer'; });
                    chart.on('mouseout', () => { chart.getDom().style.cursor = 'default'; });
                }
                const legendEl = $('sovLegendItems');
                if (legendEl) { legendEl.innerHTML = data.map((m, i) => { const pctF = totalAll > 0 ? m.total / totalAll * 100 : 0; const pctD = pctF === 0 ? '0%' : pctF < 1 ? '<1%' : pctF.toFixed(1) + '%'; const k = DOCfg.mediaKeyMap[m.media] || DOCfg.mediaKeyMap[m.media_key] || ''; return `<div class="do-sov-item" ${k ? `onclick="DOPanel.open('${k}','all')" title="Lihat mentions ${m.media}"` : ''}><div class="do-sov-item-row"><span class="do-sov-dot" style="background:${colors[i]};"></span><span class="do-sov-name">${m.media}</span><span class="do-sov-pct" style="color:${colors[i]};">${pctD}</span></div><div class="do-sov-bar-wrap"><div class="do-sov-bar" style="width:${Math.max(pctF, pctF > 0 ? 2 : 0)}%;background:${colors[i]};"></div></div></div>`; }).join(''); }
            },

            _apexSentiment: null,

            async loadSentLine() {
                const r = await fetch(`/mk/api/sentiment-timeline?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`);
                const d = await r.json();
                $('skSentiment').style.display = 'none';

                const xLabels = (d.dates || []).map(dt => { try { const o = new Date(dt + 'T00:00:00'); return `${o.getDate()}/${o.getMonth() + 1}`; } catch (e) { return dt; } });
                const sentNames = ['Total', 'Positive', 'Neutral', 'Negative'];
                const sentMap = { Total: 'all', Positive: 'pos', Neutral: 'neu', Negative: 'neg' };

                const options = {
                    chart: {
                        type: 'area',
                        height: 360,
                        fontFamily: 'inherit',
                        background: 'transparent',
                        toolbar: { show: false },
                        animations: {
                            enabled: true,
                            easing: 'linear',
                            dynamicAnimation: { speed: 1000 }
                        },
                        events: {
                            markerClick: (e, ctx, cfg) => {
                                DOPanel.open('all', sentMap[sentNames[cfg.seriesIndex]] || 'all');
                            },
                        },
                    },
                    series: [
                        { name: 'Total', data: d.values || [] },
                        { name: 'Positive', data: d.sentiment?.positive || [] },
                        { name: 'Neutral', data: d.sentiment?.neutral || [] },
                        { name: 'Negative', data: d.sentiment?.negative || [] },
                    ],
                    colors: ['#4680ff', '#10B981', '#94A3B8', '#EF4444'],
                    xaxis: {
                        categories: xLabels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { fontFamily: 'inherit', fontSize: '11px', fontWeight: 600, colors: '#94A3B8' } }
                    },
                    yaxis: {
                        labels: {
                            formatter: v => numK(v),
                            style: { fontFamily: 'inherit', fontSize: '10px', fontWeight: 600, colors: '#94A3B8' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    fill: { opacity: 0.3 },
                    stroke: { curve: 'smooth', width: 2.5 },
                    markers: {
                        size: xLabels.length <= 20 ? 5 : 0,
                        strokeWidth: 2,
                        strokeColors: '#fff',
                        hover: { size: 7 }
                    },
                    dataLabels: {
                        enabled: xLabels.length <= 20,
                        formatter: v => v > 0 ? numK(v) : '',
                        style: {
                            fontSize: '10px',
                            fontFamily: 'inherit',
                            fontWeight: '700',
                        },
                        background: {
                            enabled: true,
                            borderRadius: 3,
                            borderWidth: 0,
                            padding: 3,
                            opacity: 0.9,
                        },
                        offsetY: -6,
                    },
                    grid: {
                        borderColor: 'rgba(226,232,240,.55)',
                        strokeDashArray: 3,
                        xaxis: { lines: { show: false } }
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'left',
                        fontFamily: 'inherit',
                        fontSize: '11px',
                        fontWeight: '600',
                        labels: { colors: '#94A3B8' },
                        markers: { width: 9, height: 9, radius: 50 },
                        itemMargin: { horizontal: 14, vertical: 4 }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        style: { fontFamily: 'inherit', fontSize: '12px' },
                        y: { formatter: v => numFmt(v) + ' mentions' }
                    },
                };

                if (this._apexSentiment) { try { this._apexSentiment.destroy(); } catch (e) { } }
                const el = $('chSentiment');
                if (!el) return;
                el.innerHTML = '';
                el.style.cursor = 'pointer';
                this._apexSentiment = new ApexCharts(el, options);
                this._apexSentiment.render();

                el.addEventListener('click', (e) => {
                    const target = e.target;
                    const isMarker = target.classList.contains('apexcharts-marker');
                    const isLabel = target.closest('.apexcharts-data-labels') || target.closest('.apexcharts-datalabel');
                    if (!isMarker && !isLabel) { DOPanel.open('all', 'all'); }
                });
            },

            async loadMap() {
                const r = await fetch(`/mk/api/geo-users?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d = await r.json();
                $('mapSkel').style.display = 'none';
                const rows = d.data || [];
                const primary = getPrimary();
                const mapResult = this.renderMap('buzzMap', rows, primary);
                this.buildLocationPanel('buzzMapList', rows, mapResult);
            },
            renderMap(elId, rows, primary) {
                const map = L.map(elId, { center: [-2.5, 118], zoom: 5, scrollWheelZoom: false });
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '© OpenStreetMap, © CARTO', subdomains: 'abcd', maxZoom: 19 }).addTo(map);
                if (!rows.length) return { map, markerRefs: [] };
                const maxCount = Math.max(...rows.map(p => parseInt(p.count || 0)));
                const markerRefs = [];
                rows.forEach(p => {
                    const lat = parseFloat(p.latitude || 0), lng = parseFloat(p.longitude || 0);
                    if (lat === 0 && lng === 0) { markerRefs.push(null); return; }
                    const name = p.name || 'Unknown', count = parseInt(p.count || 0);
                    if (count >= 10) L.circle([lat, lng], { radius: Math.max(5000, Math.min(Math.sqrt(count) * 2500, 50000)), fillColor: primary, color: primary, weight: 1, opacity: .2, fillOpacity: Math.min(.15 + (count / maxCount) * .4, .55) }).addTo(map);
                    const pin = L.marker([lat, lng], { icon: L.divIcon({ className: '', html: `<div style="width:12px;height:12px;background:${primary};border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>`, iconSize: [12, 12], iconAnchor: [6, 6] }) }).addTo(map).bindPopup(`<div style="font-family:inherit;text-align:center;padding:6px;"><div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:5px;">${name}</div><div style="font-size:20px;font-weight:800;color:${primary};">${count.toLocaleString()}</div><div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">mentions</div></div>`);
                    markerRefs.push({ marker: pin, lat, lng });
                    const lbl = count > 999 ? (count / 1000).toFixed(1) + 'k' : count;
                    L.marker([lat, lng], { icon: L.divIcon({ className: '', html: `<div style="font-family:inherit;font-size:10px;font-weight:800;color:#fff;background:${primary};padding:2px 7px;border-radius:3px;border:1.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);white-space:nowrap;">${lbl}</div>`, iconSize: [36, 18], iconAnchor: [18, 24] }), interactive: false }).addTo(map);
                });
                return { map, markerRefs };
            },
            buildLocationPanel(listId, rows, mapResult) {
                const listEl = $(listId); if (!listEl) return;
                const { map, markerRefs } = mapResult;
                const valid = rows.filter(p => !(parseFloat(p.latitude || 0) === 0 && parseFloat(p.longitude || 0) === 0));
                if (!valid.length) { listEl.innerHTML = '<div class="do-empty" style="padding:20px 12px;font-size:11px;">No location data</div>'; return; }
                const sorted = [...valid].sort((a, b) => parseInt(b.count || 0) - parseInt(a.count || 0));
                listEl.innerHTML = sorted.map((p, rank) => { const name = p.name || 'Unknown', count = parseInt(p.count || 0); const lbl = count > 999 ? (count / 1000).toFixed(1) + 'k' : count; return `<div class="do-loc-item" data-name="${name}"><span class="do-loc-rank">${rank + 1}</span><div class="do-loc-info"><div class="do-loc-name" title="${name}">${name}</div><div class="do-loc-count">${lbl} mentions</div></div><div class="do-loc-dot"></div></div>`; }).join('');
                listEl.querySelectorAll('.do-loc-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const name = item.dataset.name, target = valid.find(p => (p.name || 'Unknown') === name); if (!target) return;
                        const lat = parseFloat(target.latitude || 0), lng = parseFloat(target.longitude || 0); if (lat === 0 && lng === 0) return;
                        map.flyTo([lat, lng], 8, { animate: true, duration: 1 });
                        const ref = markerRefs.find(r => r && Math.abs(r.lat - lat) < .001 && Math.abs(r.lng - lng) < .001);
                        if (ref) setTimeout(() => ref.marker.openPopup(), 800);
                        listEl.querySelectorAll('.do-loc-item').forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                    });
                });
            }
        };

        /* ══ DATA EXPORTER ══ */
        const DataExporter = {
            _bar: null, _title: null, _sub: null,
            _show(title, sub, pct) { if (!this._bar) { this._bar = $('doExportProgBar'); this._title = $('doExportProgTitle'); this._sub = $('doExportProgSub'); } $('doExportOverlay').classList.add('show'); if (this._title) this._title.textContent = title || 'Mempersiapkan…'; if (this._sub) this._sub.textContent = sub || ''; if (this._bar) this._bar.style.width = (pct || 0) + '%'; },
            _hide() { $('doExportOverlay').classList.remove('show'); },
            _prog(pct, sub) { if (this._bar) this._bar.style.width = pct + '%'; if (sub && this._sub) this._sub.textContent = sub; },
            downloadCSV(rows, fn) { if (!rows.length) { alert('Tidak ada data.'); return; } const headers = Object.keys(rows[0]); const lines = [headers.join(';'), ...rows.map(r => headers.map(h => { let v = String(r[h] || '').replace(/"/g, '""'); return v.includes(';') || v.includes('"') || v.includes('\n') ? `"${v}"` : v; }).join(';'))]; const blob = new Blob(['\uFEFF' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' }); const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(blob), download: fn.endsWith('.csv') ? fn : fn + '.csv' }); document.body.appendChild(a); a.click(); document.body.removeChild(a); },
            downloadXLSX(rows, fn, sheet = 'Data') { if (!rows.length) { alert('Tidak ada data.'); return; } const ws = XLSX.utils.json_to_sheet(rows); const wb = XLSX.utils.book_new(); XLSX.utils.book_append_sheet(wb, ws, sheet); XLSX.writeFile(wb, fn.endsWith('.xlsx') ? fn : fn + '.xlsx'); },
            async exportAll(format = 'xlsx') {
                this._show('Mengambil semua data…', 'Menghubungi semua platform', 5);
                const q = `project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}&rows=500&start=0`;
                const platforms = ['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok'];
                const label = { doc: 'Online News', twit: 'Twitter', fb: 'Facebook', instagram: 'Instagram', youtube: 'YouTube', tiktok: 'TikTok' };
                let allRows = [], step = 0;
                for (const plat of platforms) {
                    step++; this._prog(10 + step * 13, 'Mengambil ' + label[plat] + '…');
                    try {
                        let items = [];
                        if (plat === 'instagram') { for (const sub of ['postbylike', 'postbycomment', 'postbydate', '']) { try { const r = await fetch(`/mk/api/news/ig-top-status?${q}${sub ? '&sub=' + sub : ''}`); const d = await r.json(); items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []); if (items.length) break; } catch (e) { continue; } } }
                        else { const eps = { doc: `/mk/api/news/mentions?${q}`, twit: `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`, fb: `/mk/api/news/fb-top-status?${q}&sub=fblike`, youtube: `/mk/api/news/ytb-top-status?${q}`, tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike` }; const r = await fetch(eps[plat]); const d = await r.json(); items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []); if (plat === 'doc') items = items.filter(m => { const tc = String(m.tcode || '').toLowerCase(), mt = String(m.media_type || '').toLowerCase(); return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online' || mt === 'article'; }); }
                        items.forEach(item => { const sentRaw = String(item.class_sentiment || item.sentiment || '0').toLowerCase(); const sent = sentRaw === '1' || sentRaw === 'positive' || sentRaw === 'positif' ? 'Positif' : sentRaw === '-1' || sentRaw === '2' || sentRaw === 'negative' || sentRaw === 'negatif' ? 'Negatif' : 'Netral'; const nameRaw = (plat === 'fb' ? item.from_name || item.page_name : plat === 'instagram' ? item.username || item.user_name : plat === 'tiktok' ? item.author_nickname || item.nickname : plat === 'youtube' ? item.channel_title || item.channel_name : null); allRows.push({ platform: label[plat], author: (nameRaw || item.author_name || item.channel_name || item.publisher || item.source_name || '').trim(), handle: item.author_scr_name || item.screen_name || item.username || '', sentimen: sent, tanggal: item.date_created || '', konten: (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim().slice(0, 500), url: item.url || item.link || '', likes: parseInt(item.num_likes || item.likes || item.favorite_count || 0), shares: parseInt(item.num_retweeted || item.shares || item.retweet_count || item.share_count || 0), views: parseInt(item.num_views || item.views || item.play_count || 0), comments: parseInt(item.num_comments || item.comment_count || 0) }); });
                    } catch (e) { console.warn('Export error', plat, e); }
                }
                this._prog(90, 'Membuat file…'); const fn = `DataOverview_${DOCfg.sd}_${DOCfg.ed}`;
                if (format === 'csv') this.downloadCSV(allRows, fn); else this.downloadXLSX(allRows, fn, 'All Media');
                this._hide();
            },
            async exportPlatform(platform) {
                const label = { doc: 'Online_News', twit: 'Twitter', fb: 'Facebook', instagram: 'Instagram', youtube: 'YouTube', tiktok: 'TikTok' };
                this._show('Mengambil data ' + label[platform], 'Menghubungi server…', 20);
                const q = `project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}&rows=500&start=0`;
                try {
                    let items = [];
                    if (platform === 'instagram') { for (const sub of ['postbylike', 'postbycomment', 'postbydate', '']) { try { const r = await fetch(`/mk/api/news/ig-top-status?${q}${sub ? '&sub=' + sub : ''}`); const d = await r.json(); items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []); if (items.length) break; } catch (e) { continue; } } }
                    else { const eps = { doc: `/mk/api/news/mentions?${q}`, twit: `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`, fb: `/mk/api/news/fb-top-status?${q}&sub=fblike`, youtube: `/mk/api/news/ytb-top-status?${q}`, tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike` }; const r = await fetch(eps[platform]); const d = await r.json(); items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []); if (platform === 'doc') items = items.filter(m => { const tc = String(m.tcode || '').toLowerCase(), mt = String(m.media_type || '').toLowerCase(); return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online' || mt === 'article'; }); }
                    this._prog(80, 'Membuat file…');
                    const rows = items.map(item => { const sentRaw = String(item.class_sentiment || item.sentiment || '0').toLowerCase(); const sent = sentRaw === '1' || sentRaw === 'positive' || sentRaw === 'positif' ? 'Positif' : sentRaw === '-1' || sentRaw === '2' || sentRaw === 'negative' || sentRaw === 'negatif' ? 'Negatif' : 'Netral'; return { author: (item.author_name || item.channel_name || item.from_name || item.publisher || item.source_name || '').trim(), handle: item.author_scr_name || item.screen_name || item.username || '', sentimen: sent, tanggal: item.date_created || '', konten: (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim().slice(0, 500), url: item.url || item.link || '' }; });
                    this.downloadCSV(rows, `${label[platform]}_${DOCfg.sd}_${DOCfg.ed}`);
                } catch (e) { alert('Gagal export: ' + e.message); }
                this._hide();
            }
        };

        /* ══ EXPORT DROPDOWN ══ */
        $('doExportMasterBtn')?.addEventListener('click', e => { e.stopPropagation(); $('doExportDd')?.classList.toggle('show'); });
        document.addEventListener('click', e => { const dd = $('doExportDd'); if (dd?.classList.contains('show') && !e.target.closest('.do-export-wrap')) dd.classList.remove('show'); });

        /* ══ PLATFORM PICKER DISMISS ══ */
        document.addEventListener('mousedown', e => { const pp = $('doPlatPicker'); if (pp?.classList.contains('show') && !pp.contains(e.target)) pp.classList.remove('show'); });

        /* ══ BOOT ══ */
        document.addEventListener('DOMContentLoaded', () => {
            DOLoader.init();
        });
    </script>
@endsection