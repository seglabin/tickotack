		

		<script src="{{ asset('assets/templeApexa/js/jquery-3.7.1.min.js')}}"></script>
		<!-- <script src="{{ asset('assets/templeApexa/js/jquery.min.js')}}"></script> -->
		<!-- <script src="{{ asset('assets/templeApexa/js/vendor/jquery-3.6.0.min.js')}}"></script> -->
		<script src="{{ asset('assets/templeApexa/js/bootstrap.bundle.min.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/jquery.magnific-popup.min.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/jquery.odometer.min.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/jquery.appear.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/gsap.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/ScrollTrigger.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/SplitText.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/gsap-animation.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/jquery.parallaxScroll.min.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/swiper-bundle.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/ajax-form.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/wow.min.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/aos.js')}}"></script>
		
		
		
		
		<!-- Select2         -->
		<script src="{{ asset('assets/templeApexa/js/select2.min.js')}}"></script>
		
		
		<!-- <script src="{{ asset('assets/templeApexa/js/jquery.min.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/bootstrap.min.js')}}"></script> -->
		
		
		
		<!-- DataTables   -->
		<script src="{{ asset('assets/templeApexa/plugins/datatables/jquery.dataTables.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/jszip/jszip.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/pdfmake/pdfmake.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/pdfmake/vfs_fonts.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
		<script src="{{ asset('assets/templeApexa/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
		
		
		
		<script src="{{ asset('assets/templeApexa/js/bootstrap-fileupload.js') }}"></script>
		
		<script src="{{ asset('assets/templeApexa/js/main.js')}}"></script>
		<script src="{{ asset('assets/templeApexa/js/fonction.js') }}"></script>

		<script>
			const text = document.querySelector(".circle");
			if (text) {
				text.innerHTML = text.textContent.replace(/\S/g, "<span>$&</span>");
				const element = document.querySelectorAll(".circle span");
				for (let i = 0; i < element.length; i++) {
					element[i].style.transform = "rotate(" + i * 17 + "deg)";
				}
			}
		</script>