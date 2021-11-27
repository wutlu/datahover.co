@extends(
	'layouts.master',
	[
		'title' => 'Terms of Service'
	]
)

@section('content')
	@component('includes.header')
		@slot('title', 'Terms of Service')
		<br />
		<br />
		<br />
	@endcomponent

	<div class="container">
		<div class="mw-1024px mx-auto my-5 py-5">
			<h4 class="mb-2 fw-bold">Introduction</h4>

			<p class="lead mb-4">Following are the Terms of Service for Datahover’s services (“Service,” “Datahover,” “we,” or “us”).  These terms and conditions govern your use of this website (“website”); by using this website, you accept these terms and conditions in full.   If you disagree with these terms and conditions or any part of these terms and conditions, you must not use this website.</p>
			<p class="lead mb-4">This website uses cookies. By using this website and agreeing to these terms and conditions, you consent to our Datahover’s use of cookies in accordance with the terms of Datahover’s <a class="link-dark fw-bold" href="{{ route('page', [ 'base' => 'legal', 'name' => 'privacy-policy' ]) }}">privacy policy</a>.</p>

			<h4 class="mb-2 fw-bold">License to use website</h4>
			<p class="lead mb-4">Unless otherwise stated, Datahover and/or its licensors own the intellectual property rights in the website and material on the website.  Subject to the license below, all these intellectual property rights are reserved.</p>
			<p class="lead mb-4">Datahover hereby grants you a non-exclusive, non-transferable subscription to use the Service during the Term, solely for your internal use in accordance with the use parameters described in the order form utilized to order such subscription and subject to these Terms of Service.</p>
			<p class="lead mb-4">By using Datahover to collect data from and optimize your social media channels, you’re also agreeing to be bound by their Terms of Service. YouTube’s Terms of Service can be found <a class="link-dark fw-bold" target="_blank" href="https://www.youtube.com/t/terms">here</a>.</p>

			<h4 class="mb-2 fw-bold">Setting up Your Account</h4>
			<p class="lead mb-4">You must have a <a class="link-dark fw-bold" target="_blank" href="https://github.com/">GitHub</a> account to sign up or login to Datahover. When Datahover is paired with <a class="link-dark fw-bold" target="_blank" href="https://github.com/">GitHub</a>, it only receives email, username, profile picture, and <a class="link-dark fw-bold" target="_blank" href="https://github.com/">GitHub</a> ID.</p>
			<p class="lead mb-4">You must provide accurate and complete information, including your legal full name, a working email address, and any other information requested during the account signup process to obtain an account and use the Service, and update this information if it changes.</p>
			<p class="lead mb-4">You must be a human being to set up an account and use the Service. Accounts may not be created by “bots” or other automated methods.</p>
			<p class="lead mb-4">You are responsible for keeping your account and password secure, and are also responsible for all activities using your account or password. Datahover is not liable for any loss or damage that results from your failure to comply with this obligation or unauthorized use of your account.</p>
			<p class="lead mb-4">You may never use another’s account without permission.</p>
			<p class="lead mb-4">You must notify Datahover immediately of any breach of security or unauthorized use of your account.</p>

			<h4 class="mb-2 fw-bold">Fees and Payment</h4>
			<p class="lead mb-4">Payment and pricing terms for the Service are as specified in the order form utilized to order such subscription.</p>
			<p class="lead mb-4">By selecting a premium service you agree to pay Datahover the monthly or annual subscription fees indicated for that service. Payments will be charged on the day you sign up for a premium service and will cover the use of that service for a monthly or annual period as indicated. Premium service fees are not refundable.</p>
			<p class="lead mb-4">Datahover offers monthly and annual subscriptions that renew until they are cancelled by the user. Your subscription will automatically renew for another period unless notice of non-renewal is provided. Your notice of non-renewal must be received before the renewal period begins.</p>
			<p class="lead mb-4">Before you start your subscription, you need to top up your Datahover account. The balance you upload is billed in bulk. If you want to delete your membership, the balance in your account will not be refunded. To change between subscription packages, we only deduct the 1-day package fee from your balance.</p>
			<p class="lead mb-4">Prices for the Service are subject to change upon 30 days notice from Datahover. This notice may be posted on the Datahover website or may appear with the Service itself. If you do not agree to the price change(s), you may cancel your account during this 30-day period. By continuing to use the Service after the effective date of a pricing change, you thereby agree to such pricing change.</p>
			<p class="lead mb-4">Datahover shall not be liable to you or to any third party for any modifications to the Service or prices.</p>

			<h4 class="mb-2 fw-bold">Cancellation and Termination</h4>
			<p class="lead mb-4">You are solely responsible for properly closing your account.</p>
			<p class="lead mb-4">You can close your account at any time by emailing us at <a class="link-dark fw-bold" href="mailto:{{ config('etsetra.email') }}">{{ config('etsetra.email') }}</a>.</p>
			<p class="lead mb-4">Payments are made for the upcoming billing cycle. Datahover does not provide pro-rated refunds for accounts that are cancelled during their subscription term.</p>

			<h4 class="mb-2 fw-bold">Acceptable use</h4>
			<p class="lead mb-4">You must not use this website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of the website; or in any way which is unlawful, illegal, fraudulent or harmful, or in connection with any unlawful, illegal, fraudulent or harmful purpose or activity.</p>
			<p class="lead mb-4">You must not use this website to copy, store, host, transmit, send, use, publish or distribute any material which consists of (or is linked to) any spyware, computer virus, Trojan horse, worm, keystroke logger, rootkit or other malicious computer software.</p>
			<p class="lead mb-4">You must not conduct any systematic or automated data collection activities (including without limitation scraping, data mining, data extraction and data harvesting) on or in relation to this website without Datahover’s express written consent.</p>
			<p class="lead mb-4">You must not use this website to transmit or send unsolicited commercial communications.</p>

			<h4 class="mb-2 fw-bold">Restricted access</h4>
			<p class="lead mb-4">Access to certain areas of this website is restricted.  Datahover reserves the right to restrict access to areas of this website, or indeed this entire website, at Datahover’s discretion.</p>
			<p class="lead mb-4">If Datahover provides you with a user ID and password to enable you to access restricted areas of this website or other content or services, you must ensure that the user ID and password are kept confidential.</p>
			<p class="lead mb-4">Datahover may disable your user ID and password in Datahover’s sole discretion without notice or explanation.</p>

			<h4 class="mb-2 fw-bold">Limitations of liability</h4>
			<p class="lead mb-4">Datahover will not be liable to you (whether under the law of contact, the law of torts or otherwise) in relation to the contents of, or use of, or otherwise in connection with, this website:</p>
			<ul class="mb-4">
				<li>for any direct loss;</li>
				<li>for any indirect, special or consequential loss; or</li>
				<li>for any business losses, loss of revenue, income, profits or anticipated savings, loss of contracts or business relationships, loss of reputation or goodwill, or loss or corruption of information or data.</li>
			</ul>
			<p class="lead mb-4">These limitations of liability apply even if Datahover has been expressly advised of the potential loss.</p>

			<h4 class="mb-2 fw-bold">Exceptions</h4>
			<p class="lead mb-4">Nothing in this website disclaimer will exclude or limit any warranty implied by law that it would be unlawful to exclude or limit; and nothing in this website disclaimer will exclude or limit Datahover’s liability in respect of any:</p>
			<ul class="mb-4">
				<li>death or personal injury caused by Datahover’s negligence;</li>
				<li>fraud or fraudulent misrepresentation on the part of Datahover; or</li>
				<li>matter which it would be illegal or unlawful for Datahover to exclude or limit, or to attempt or purport to exclude or limit, its liability.</li>
			</ul>

			<h4 class="mb-2 fw-bold">Reasonableness</h4>
			<p class="lead mb-4">By using this website, you agree that the exclusions and limitations of liability set out in this website disclaimer are reasonable. </p>
			<p class="lead mb-4">If you do not think they are reasonable, you must not use this website.</p>

			<h4 class="mb-2 fw-bold">Other parties</h4>
			<p class="lead mb-4">You accept that, as a limited liability entity, Datahover has an interest in limiting the personal liability of its officers and employees.  You agree that you will not bring any claim personally against Datahover’s officers or employees in respect of any losses you suffer in connection with the website.</p>
			<p class="lead mb-4">You agree that the limitations of warranties and liability set out in this website disclaimer will protect Datahover’s officers, employees, agents, subsidiaries, successors, assigns and sub-contractors as well as Datahover.</p>

			<h4 class="mb-2 fw-bold">Unenforceable provisions</h4>
			<p class="lead mb-4">If any provision of this website disclaimer is, or is found to be, unenforceable under applicable law, that will not affect the enforceability of the other provisions of this website disclaimer.</p>

			<h4 class="mb-2 fw-bold">Indemnity</h4>
			<p class="lead mb-4">You hereby indemnify Datahover and undertake to keep Datahover indemnified against any losses, damages, costs, liabilities and expenses (including without limitation legal expenses and any amounts paid by Datahover to a third party in settlement of a claim or dispute on the advice of Datahover’s legal advisers) incurred or suffered by Datahover arising out of any breach by you of any provision of these terms and conditions, or arising out of any claim that you have breached any provision of these terms and conditions.</p>

			<h4 class="mb-2 fw-bold">Breaches of these terms and conditions</h4>
			<p class="lead mb-4">Without prejudice to Datahover’s other rights under these terms and conditions, if you breach these terms and conditions in any way, Datahover may take such action as Datahover deems appropriate to deal with the breach, including suspending your access to the website, prohibiting you from accessing the website, blocking computers using your IP address from accessing the website, contacting your internet service provider to request that they block your access to the website and/or bringing court proceedings against you.</p>

			<h4 class="mb-2 fw-bold">Variation</h4>
			<p class="lead mb-4">Datahover may revise these terms and conditions from time-to-time.  Revised terms and conditions will apply to the use of this website from the date of the publication of the revised terms and conditions on this website.  Please check this page regularly to ensure you are familiar with the current version.</p>

			<h4 class="mb-2 fw-bold">Assignment</h4>
			<p class="lead mb-4">Datahover may transfer, sub-contract or otherwise deal with Datahover’s rights and/or obligations under these terms and conditions without notifying you or obtaining your consent.</p>
			<p class="lead mb-4">You may not transfer, sub-contract or otherwise deal with your rights and/or obligations under these terms and conditions.</p>

			<h4 class="mb-2 fw-bold">Severability</h4>
			<p class="lead mb-4">If a provision of these terms and conditions is determined by any court or other competent authority to be unlawful and/or unenforceable, the other provisions will continue in effect. If any unlawful and/or unenforceable provision would be lawful or enforceable if part of it were deleted, that part will be deemed to be deleted, and the rest of the provision will continue in effect.</p>

			<h4 class="mb-2 fw-bold">Entire agreement</h4>
			<p class="lead mb-4">These terms and conditions constitute the entire agreement between you and Datahover in relation to your use of this website, and supersede all previous agreements in respect of your use of this website.</p>

			<h4 class="mb-2 fw-bold">Datahover’s details</h4>
			<p class="lead mb-4">Datahover is a service offered by <span class="fw-bold">ETSETRA LTD</span>.</p>
			<p class="lead mb-4">You can contact Datahover by email at <a class="link-dark fw-bold" href="mailto:{{ config('etsetra.email') }}">{{ config('etsetra.email') }}</a>.</p>
		</div>
	</div>

	@include('includes.footer')
@endsection
