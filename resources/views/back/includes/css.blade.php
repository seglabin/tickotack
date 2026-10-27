<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/bootstrap.min.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/animate.min.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/magnific-popup.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/fontawesome-all.min.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/flaticon.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/odometer.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/swiper-bundle.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/aos.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/default.css')}}" />
		<link rel="stylesheet" href="{{ asset('assets/templeApexa/css/main.css')}}" />


        <!-- Select2         -->
        <link href="{{ asset('assets/templeApexa/css/select2.min.css')}}" rel="stylesheet">

        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('assets/templeApexa/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/templeApexa/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/templeApexa/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">


        <link rel="stylesheet" href="{{ asset('assets/templeApexa/css/bootstrap-fileupload.css') }}">        


<style>

     .letitre {
            text-align: center;
            padding-bottom: 10px;
            /* padding-bottom: 60px; */
            position: relative;
        }

        .btnarrondi {
            border-radius: 20px;
        }

        .lelabel {
            font-size: 16px;
            font-weight: bold;
        }

        .btn-sm,
        .table .btn {
            padding: 6px 12px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            line-height: 1.4 !important;
            height: auto !important;
            min-height: auto !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .table .btn i,
        .btn-sm i {
            font-size: 14px !important;
        }

        /* Réduction des espaces horizontaux et verticaux entre les champs des formulaires */
        .contact__form-wrap .row {
            --bs-gutter-x: 12px !important;
            margin-left: -6px !important;
            margin-right: -6px !important;
        }
        .contact__form-wrap .row > [class*="col-"] {
            padding-left: 6px !important;
            padding-right: 6px !important;
        }
        .contact__form-wrap .card-body {
            padding: 4px 0px 8px 0px !important;
        }
        .contact__form-wrap .form-group {
            margin-bottom: 8px !important;
        }
        .contact__form-wrap .lelabel,
        .contact__form-wrap label {
            margin-bottom: 4px !important;
            display: inline-block;
        }

        body.back-office .transparent-header {
            position: relative;
            z-index: 20;
            background: #fff;
        }

        body.back-office #sticky-header {
            background: #fff;
        }

        body.back-office main.fix {
            min-height: 60vh;
            padding: 20px 0 48px;
            background: #f4f7fb;
        }

        body.back-office .about__area-five {
            padding: 0;
        }

        body.back-office .about__content-five {
            padding: clamp(16px, 3vw, 32px);
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(31, 45, 61, 0.08);
        }

        body.back-office .section-title {
            margin-bottom: 20px;
        }

        body.back-office .back-mobile-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        body.back-office .back-mobile-actions .btn {
            min-height: 44px;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
        }

        body.back-office .section-title .title {
            font-size: clamp(24px, 3vw, 34px);
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        body.back-office .contact__form-wrap .form-control,
        body.back-office .contact__form-wrap .form-select,
        body.back-office .contact__form-wrap select,
        body.back-office .contact__form-wrap textarea {
            min-height: 42px;
            max-width: 100%;
        }

        body.back-office .table-responsive,
        body.back-office .dataTables_wrapper {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        body.back-office .table-responsive > .table,
        body.back-office .dataTables_wrapper table {
            min-width: 600px;
        }

        body.back-office .mobile-nav-toggler {
            flex: 0 0 46px;
            border: 0;
            padding: 0;
        }

        body.back-office .tgmobile__menu {
            width: min(360px, 100vw);
            padding-right: 0;
        }

        body.back-office .tgmobile__menu .tgmobile__menu-box {
            padding-bottom: 24px;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        body.back-office .tgmobile__menu .tgmobile__menu-outer,
        body.back-office .tgmobile__menu .tgmenu__navbar-wrap {
            display: block;
            width: 100%;
        }

        body.back-office .tgmobile__menu .tgmenu__navbar-wrap > ul {
            display: block;
            width: 100%;
            margin: 0;
        }

        body.back-office .tgmenu__main-menu .menu-item-has-children:focus-within > .sub-menu,
        body.back-office .tgmenu__main-menu .menu-item-has-children:focus-within > .sub-menu > .menu-item-has-children:focus-within > .sub-menu {
            opacity: 1;
            visibility: visible;
            transform: scale(1);
        }

        body.back-office .tgmobile__menu .navigation li.menu-item-has-children .dropdown-btn {
            border: 0;
            padding: 0;
            touch-action: manipulation;
        }

        body.back-office .tgmobile__menu .close-btn {
            align-self: flex-end;
            border: 0;
            background: transparent;
        }

        body.back-office .tgmobile__menu .user-menu-dropdown {
            padding: 10px 16px;
        }

        body.back-office .tgmobile__menu .user-menu-toggle {
            color: #172033 !important;
            background: #f4f6fb !important;
            border-color: #e5e9f2 !important;
        }

        body.back-office .tgmobile__menu .user-dropdown-menu {
            position: static !important;
            min-width: 0 !important;
            width: 100%;
            margin-top: 8px;
            box-shadow: none !important;
        }

        body.back-office .tgmobile__menu .user-dropdown-menu a {
            padding: 10px 16px !important;
        }

        body.back-office .tgmobile__menu .user-dropdown-menu form {
            margin: 0;
        }

        body.back-office .modal-dialog {
            width: calc(100% - 2rem);
            margin: 1rem auto;
        }

        body.back-office .modal-content {
            max-width: 100%;
        }

        @media (max-width: 767.98px) {
            body.back-office main.fix {
                padding: 14px 0 32px;
            }

            body.back-office .about__content-five {
                padding: 16px 12px;
                border-radius: 12px;
            }

            body.back-office .about__content-five .table-responsive.card-body {
                padding: 8px 0 !important;
            }

            body.back-office .section-title {
                margin-bottom: 16px;
            }

            body.back-office .contact__form-wrap .btn,
            body.back-office .about__content-five .btn {
                min-height: 42px;
                margin-bottom: 6px;
            }
        }

        @media (max-width: 575.98px) {
            body.back-office .modal {
                overflow-x: hidden;
                overflow-y: auto;
            }

            body.back-office .modal-dialog,
            body.back-office .modal-dialog.modal-lg,
            body.back-office .modal-dialog.modal-xl {
                width: calc(100% - 1rem);
                max-width: none;
                margin: .5rem auto;
            }

            body.back-office .modal-content {
                max-height: calc(100vh - 1rem);
                max-height: calc(100dvh - 1rem);
            }

            body.back-office .modal-body {
                min-height: 0;
                overflow-y: auto;
                padding: 1rem !important;
                -webkit-overflow-scrolling: touch;
            }

            body.back-office .modal-header,
            body.back-office .modal-footer {
                gap: .5rem;
                flex-wrap: wrap;
                padding: .75rem 1rem;
            }

            body.back-office .modal-footer > .btn {
                flex: 1 1 auto;
            }

            body.back-office .modal .table-responsive {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
</style>