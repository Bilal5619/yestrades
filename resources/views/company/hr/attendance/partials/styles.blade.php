<style>
    .attendance-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 24px;
    }

    .attendance-header h1 {
        margin: 0 0 7px;
        font-size: 28px;
    }

    .attendance-header p {
        margin: 0;
        color: #aab4ca;
    }

    .attendance-add,
    .attendance-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 7px;
        color: #ffffff;
        text-decoration: none;
    }

    .attendance-add {
        background: #2878dc;
    }

    .attendance-back {
        background: #3a4967;
    }

    .attendance-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 18px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .attendance-filter-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .attendance-filter-field label {
        color: #c9d2e5;
        font-size: 12px;
        font-weight: 600;
    }

    .attendance-filter-field input,
    .attendance-filter-field select {
        padding: 9px 11px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
    }

    .attendance-filter-submit {
        min-height: 40px;
        padding: 0 16px;
        border: none;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }

    .attendance-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .attendance-table-wrapper {
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .attendance-table th,
    .attendance-table td {
        padding: 13px 10px;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        text-align: left;
    }

    .attendance-table th {
        color: #aab4ca;
        font-size: 12px;
    }

    .attendance-badge {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 13px;
        font-size: 11px;
    }

    .attendance-badge-in { background: rgba(46,191,114,0.16); color: #59dc96; }
    .attendance-badge-out { background: rgba(255,179,71,0.16); color: #ffc46f; }
    .attendance-badge-manual { background: rgba(170,180,202,0.14); color: #c4ccdc; }

    .attendance-empty {
        padding: 35px;
        text-align: center;
        color: #aab4ca;
    }

    .attendance-form-card {
        padding: 24px;
        border-radius: 10px;
        background: #1e2a44;
    }

    .attendance-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 18px;
    }

    .attendance-field label {
        color: #c9d2e5;
        font-size: 13px;
        font-weight: 600;
    }

    .attendance-field input,
    .attendance-field select,
    .attendance-field textarea {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 7px;
        background: #151f35;
        color: #ffffff;
        box-sizing: border-box;
    }

    .attendance-field textarea {
        resize: vertical;
        min-height: 90px;
    }

    .attendance-error {
        color: #ff8a92;
        font-size: 12px;
    }

    .attendance-help {
        color: #aab4ca;
        font-size: 12px;
        margin-bottom: 18px;
        line-height: 1.6;
    }

    .attendance-form-actions {
        display: flex;
        justify-content: flex-end;
    }

    .attendance-save {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 43px;
        padding: 0 20px;
        border: none;
        border-radius: 7px;
        background: #2878dc;
        color: #ffffff;
        cursor: pointer;
    }
    .attendance-quick-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 14px;
}

.attendance-period-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 0 14px;
    border-radius: 7px;
    background: #1e2a44;
    color: #cbd4e8;
    text-decoration: none;
}

.attendance-period-button:hover,
.attendance-period-button.active {
    background: #2878dc;
    color: #ffffff;
}


.attendance-summary-grid {
    display: grid;
    grid-template-columns:
        repeat(
            4,
            minmax(0, 1fr)
        );
    gap: 14px;
    margin-bottom: 20px;
}

.attendance-summary-card {
    padding: 18px;
    border-radius: 10px;
    background: #1e2a44;
}

.attendance-summary-card span {
    display: block;
    margin-bottom: 8px;
    color: #aab4ca;
    font-size: 12px;
}

.attendance-summary-card strong {
    font-size: 23px;
    color: #ffffff;
}

.attendance-green {
    color: #59dc96 !important;
}

.attendance-red {
    color: #ff7c85 !important;
}


.attendance-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.attendance-section-heading h3 {
    margin: 0 0 5px;
}

.attendance-section-heading p {
    margin: 0;
    color: #aab4ca;
    font-size: 12px;
}


.attendance-summary-table {
    min-width: 1000px;
}

.attendance-clickable-row {
    cursor: pointer;
    transition:
        background 0.15s ease;
}

.attendance-clickable-row:hover {
    background:
        rgba(
            255,
            255,
            255,
            0.04
        );
}

.attendance-row-arrow {
    width: 25px;
    text-align: right !important;
    color: #72809e;
}

.attendance-clickable-row:hover
.attendance-row-arrow {
    color: #ffffff;
}


.attendance-source-badge {
    display: inline-flex;
    padding: 4px 9px;
    border-radius: 13px;
    background:
        rgba(
            40,
            120,
            220,
            0.15
        );
    color: #78aff1;
    font-size: 11px;
}


.attendance-status {
    display: inline-flex;
    padding: 4px 9px;
    border-radius: 13px;
    font-size: 11px;
    font-weight: 600;
}

.attendance-status-complete {
    background:
        rgba(
            46,
            191,
            114,
            0.16
        );
    color: #59dc96;
}

.attendance-status-issue {
    background:
        rgba(
            255,
            99,
            99,
            0.15
        );
    color: #ff8a92;
}

.attendance-issue-text {
    margin-top: 5px;
    max-width: 230px;
    color: #ff9ca3;
    font-size: 11px;
}

.attendance-warning-text {
    color: #ffc46f;
}


.attendance-alert {
    padding: 17px 20px;
    border-radius: 9px;
    margin-bottom: 20px;
}

.attendance-alert-warning {
    border:
        1px solid
        rgba(
            255,
            179,
            71,
            0.30
        );
    background:
        rgba(
            255,
            179,
            71,
            0.08
        );
    color: #ffc46f;
}

.attendance-alert ul {
    margin-bottom: 0;
}


.attendance-timeline {
    position: relative;
}

.attendance-timeline-item {
    display: grid;
    grid-template-columns:
        75px
        18px
        1fr;
    gap: 12px;
    min-height: 70px;
}

.attendance-timeline-time {
    padding-top: 3px;
    color: #ffffff;
    font-weight: 600;
}

.attendance-timeline-marker {
    width: 12px;
    height: 12px;
    margin-top: 5px;
    border-radius: 50%;
    position: relative;
}

.attendance-timeline-marker::after {
    content: '';
    position: absolute;
    width: 2px;
    height: 64px;
    left: 5px;
    top: 12px;
    background:
        rgba(
            255,
            255,
            255,
            0.10
        );
}

.attendance-timeline-item:last-child
.attendance-timeline-marker::after {
    display: none;
}

.attendance-timeline-marker.in {
    background: #59dc96;
}

.attendance-timeline-marker.out {
    background: #ffc46f;
}

.attendance-timeline-content {
    padding-bottom: 20px;
}

.attendance-timeline-title {
    color: #ffffff;
    font-weight: 600;
}

.attendance-timeline-meta {
    margin-top: 5px;
    color: #aab4ca;
    font-size: 12px;
    line-height: 1.7;
}

.attendance-timeline-meta a {
    color: #78aff1;
    text-decoration: none;
}


@media (
    max-width: 900px
) {
    .attendance-summary-grid {
        grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );
    }
}

@media (
    max-width: 600px
) {
    .attendance-summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>
