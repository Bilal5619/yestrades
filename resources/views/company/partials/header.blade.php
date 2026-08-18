<style>
    .company-header {
        height: 75px;
        background: #151e33;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
    }

    .company-header-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .company-header-title {
        font-size: 18px;
        font-weight: 600;
    }

    .company-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .company-header-button {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: transparent;
        color: #ffffff;
        cursor: pointer;
    }
    .company-notification-wrapper {
    position: relative;
}

.company-notification-button {
    position: relative;
}

.company-notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 19px;
    height: 19px;
    padding: 0 5px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ec5762;
    color: #ffffff;
    border: 2px solid #151e33;
    font-size: 10px;
    font-weight: 700;
}

.company-notification-dropdown {
    display: none;
    position: absolute;
    top: 52px;
    right: 0;
    width: 390px;
    max-height: 480px;
    overflow-y: auto;
    z-index: 1000;
    border-radius: 10px;
    background: #1e2a44;
    border: 1px solid
        rgba(255,255,255,.08);
    box-shadow:
        0 15px 45px
        rgba(0,0,0,.35);
}

.company-notification-dropdown.show {
    display: block;
}

.company-notification-header {
    padding: 16px 18px;
    display: flex;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px solid
        rgba(255,255,255,.08);
}

.company-notification-header span {
    color: #aab4ca;
    font-size: 12px;
}

.company-notification-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 17px;
    color: #ffffff;
    text-decoration: none;
    border-bottom: 1px solid
        rgba(255,255,255,.06);
}

.company-notification-item:hover {
    background:
        rgba(255,255,255,.04);
}

.company-notification-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-danger {
    background:
        rgba(236,87,98,.17);
    color: #ff8d95;
}

.notification-warning {
    background:
        rgba(243,166,58,.17);
    color: #ffc66d;
}

.notification-info {
    background:
        rgba(40,120,220,.17);
    color: #82b7ff;
}

.company-notification-content {
    flex: 1;
}

.company-notification-content strong {
    display: block;
    margin-bottom: 4px;
    font-size: 13px;
}

.company-notification-content span {
    display: block;
    color: #aab4ca;
    font-size: 11px;
}

.company-notification-count {
    min-width: 27px;
    height: 27px;
    padding: 0 7px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        rgba(255,255,255,.1);
    font-size: 11px;
    font-weight: 700;
}

.company-notification-empty {
    padding: 30px 20px;
    text-align: center;
    color: #aab4ca;
}

.company-notification-empty i {
    display: block;
    margin-bottom: 10px;
    color: #59dc96;
    font-size: 25px;
}

.company-notification-empty strong {
    display: block;
    margin-bottom: 5px;
    color: #ffffff;
}

.company-notification-empty span {
    font-size: 12px;
}

@media (max-width: 600px) {
    .company-notification-dropdown {
        width: 320px;
        right: -90px;
    }
}
</style>

<header class="company-header">

    <div class="company-header-left">

        <button class="company-header-button" type="button">
            <i class="fas fa-bars"></i>
        </button>

        <div class="company-header-title">
            Yes Trades Company Management
        </div>

    </div>

    <div class="company-header-right">

        <div
    class="company-notification-wrapper"
    id="companyNotificationWrapper"
>

    <button
        class="company-header-button
               company-notification-button"
        type="button"
        id="companyNotificationButton"
        title="HR Reminders"
    >
        <i class="fas fa-bell"></i>

        @if (
            ($companyHrReminderCount ?? 0) > 0
        )
            <span class="company-notification-badge">
                {{
                    $companyHrReminderCount > 99
                        ? '99+'
                        : $companyHrReminderCount
                }}
            </span>
        @endif
    </button>

    <div
        class="company-notification-dropdown"
        id="companyNotificationDropdown"
    >

        <div class="company-notification-header">
            <strong>HR Reminders</strong>

            @if (
                ($companyHrReminderCount ?? 0) > 0
            )
                <span>
                    {{ $companyHrReminderCount }}
                    action(s)
                </span>
            @endif
        </div>

        <div class="company-notification-list">

            @forelse (
                $companyHrReminders ?? []
                as $reminder
            )

                <a
                    href="{{ route(
                        $reminder['route']
                    ) }}"
                    class="company-notification-item"
                >

                    <div
                        class="
                            company-notification-icon
                            notification-{{ $reminder['type'] }}
                        "
                    >
                        @if (
                            $reminder['type']
                                === 'danger'
                        )
                            <i
                                class="fas
                                       fa-triangle-exclamation"
                            ></i>

                        @elseif (
                            $reminder['type']
                                === 'warning'
                        )
                            <i
                                class="fas
                                       fa-clock"
                            ></i>

                        @else
                            <i
                                class="fas
                                       fa-circle-info"
                            ></i>
                        @endif
                    </div>

                    <div
                        class="company-notification-content"
                    >
                        <strong>
                            {{ $reminder['title'] }}
                        </strong>

                        <span>
                            {{ $reminder['message'] }}
                        </span>
                    </div>

                    <div
                        class="company-notification-count"
                    >
                        {{ $reminder['count'] }}
                    </div>

                </a>

            @empty

                <div class="company-notification-empty">
                    <i
                        class="fas
                               fa-circle-check"
                    ></i>

                    <strong>
                        No HR actions required
                    </strong>

                    <span>
                        Your current HR checks are clear.
                    </span>
                </div>

            @endforelse

        </div>

    </div>

</div>
        <button class="company-header-button" type="button">
            <i class="fas fa-moon"></i>
        </button>

      <form method="POST"
      action="{{ route('company.logout') }}"
      onsubmit="return confirm('Are you sure you want to log out?');">

    @csrf

    <button class="company-header-button"
            type="submit"
            title="Logout">

        <i class="fas fa-right-from-bracket"></i>
    </button>

</form>

    </div>
    <script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const button =
            document.getElementById(
                'companyNotificationButton'
            );

        const dropdown =
            document.getElementById(
                'companyNotificationDropdown'
            );

        const wrapper =
            document.getElementById(
                'companyNotificationWrapper'
            );

        if (!button || !dropdown) {
            return;
        }

        button.addEventListener(
            'click',
            function (event) {
                event.stopPropagation();

                dropdown.classList.toggle(
                    'show'
                );
            }
        );

        document.addEventListener(
            'click',
            function (event) {
                if (
                    wrapper &&
                    !wrapper.contains(
                        event.target
                    )
                ) {
                    dropdown.classList.remove(
                        'show'
                    );
                }
            }
        );

    }
);
</script>

</header>