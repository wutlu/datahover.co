@extends(
	'layouts.master',
	[
		'title' => 'Privacy Policy'
	]
)

@section('content')
	@component('includes.header')
		@slot('title', 'Privacy Policy')
		<br />
		<br />
		<br />
	@endcomponent

	<div class="container">
		<div class="mw-1024px mx-auto my-5 py-5">
			<p class="lead mb-4">This Privacy Policy governs the manner in which <span class="fw-bold">ETSETRA LTD</span>. Datahover deals with user-generated content posted on sources such as social networks, news feeds, internet forums, microblogs, wikis ("Social Media Content").</p>

			<h4 class="mb-2 fw-bold">Social Media Content Collection and Usage Policy</h4>
			<p class="lead mb-4">To provide our Customers with the Services, we collect Social Media Content from different publicly-available sources. We obtain Social Media Content using proprietary parsing algorithms, via public APIs, or through the agreements with the Web site operators. Collecting Social Media Content via our web parsing algorithms, we follow the applicable Web sites robots.txt protocols. We do not circumvent technical controls in place to prevent unauthorized access, such as access to usernames and passwords or use of CAPTCHA technology. Our Web parsers are designed to limit parsing to portions of Web sites that are made public by the applicable Web site operator. Datahover’s Customers are responsible for their use of Social Media Content. We require our Customers to use Social Media Content in accordance with our End User Licensing Agreement, the terms of service of the applicable Web site from which the Social Media Content is obtained or derived, and applicable Law.</p>

			<h4 class="mb-2 fw-bold">Data Safety</h4>
			<p class="lead mb-4">Datahover uses commercially reasonable technical, physical, managerial safeguards to preserve the integrity and security of Social Media Content or Personal Information. The Company has put in place appropriate electronic, physical, and managerial procedures to safeguard and secure Social Media Content and Personal Information from loss, misuse, unauthorized access or disclosure. Datahover cannot guarantee the security of Social Media Content or Personal Information on or transmitted via the Internet.</p>

			<h4 class="mb-2 fw-bold">Personal identification information</h4>
			<h6 class="mb-2 fw-bold">Users</h6>

			<ul class="mb-4">
				<li>We may collect personal identification information from Users in a variety of ways, including, but not limited to, when Users visit our site, register on the site, place an order, subscribe to the newsletter, and in connection with other activities, services, features or resources we make available on our Site.</li>
				<li>Users may be asked for, as appropriate, name, email address, phone number, credit card information.</li>
				<li>Users may, however, visit our Site anonymously. We will collect personal identification information from Users only if they voluntarily submit such information to us.</li>
				<li>Users can always refuse to supply personal identification information, except that it may prevent them from engaging in certain Site-related activities.</li>
			</ul>

			<h6 class="mb-2 fw-bold">Social Media Authors</h6>
			<p class="lead mb-4">Datahover receives personal data from third parties such as {{ implode(', ', Arr::pluck(config('sources'), 'name')) }} for our legitimate interests of serving our customers. We are fully compliant with their public terms and conditions and trust them to protect Social Media Authors through their robust privacy policies.</p>
			<ul class="mb-4">
				<li>
					Datahover collects, uses, processes and stores information that has been made publicly available on the Social Media platforms listed above such as:
					<ol style="list-style-type: upper-roman;">
						<li>names, usernames, handles, or other identifiers;</li>
						<li>the content of the information published via that name, username, handle, or other identifier, including comments, expressions, opinions, posts, etc.;</li>
						<li>profile pictures or other images or videos posted or interacted with;</li>
						<li>publicly disclosed location;</li>
						<li>gender;</li>
						<li>We may also use this information to infer other data, such as the sentiment of the post.</li>
					</ol>
				</li>
				<li>To collect & provide customers with data about their YouTube channels, Datahover uses <a class="link-dark fw-bold" target="_blank" href="https://developers.google.com/youtube/terms/developer-policies#definition-youtube-api-services">YouTube’s API Services</a>. Google’s Privacy Policy can be found <a class="link-dark fw-bold" target="_blank" href="http://www.google.com/policies/privacy/">here</a>.</li>
			</ul>

			<h4 class="mb-2 fw-bold">Rights of Users and Social Media Authors</h4>
			<p class="lead mb-4">At any time, you may contact <a class="link-dark fw-bold" href="mailto:{{ config('etsetra.email') }}">{{ config('etsetra.email') }}</a> and request that we:</p>
			<ul class="mb-4">
				<li>Rectify personal data Datahover has collected</li>
				<li>Provide you with a copy of your personal data</li>
				<li>Permanently delete any personal or non-personal data (such as cookies) that we have collected and shared</li>
				<li>Refuse any use which relates to marketing</li>
			</ul>

			<h4 class="mb-2 fw-bold">Non-personal identification information</h4>
			<p class="lead mb-4">We may collect non-personal identification information about Users whenever they interact with our Site. Non-personal identification information may include the browser name, the type of computer and technical information about Users’ means of connection to our Site, such as the operating system and the Internet service providers utilized and other similar information.</p>

			<h4 class="mb-2 fw-bold">Web browser cookies</h4>
			<p class="lead mb-4">Our Site may use “cookies” to enhance User experience. Users’ web browsers place cookies on their hard drive for record-keeping purposes and sometimes to track information about them. The User may choose to set their web browser to refuse cookies, or to alert you when cookies are being sent. If they do so, note that some parts of the Site may not function properly.</p>
			<p class="lead mb-4">It is your right to contact <a class="link-dark fw-bold" href="mailto:{{ config('etsetra.email') }}">{{ config('etsetra.email') }}</a> and request that we delete any cookies that link back to you and your online activity.</p>

			<h4 class="mb-2 fw-bold">How we use collected information</h4>
			<h6 class="mb-2 fw-bold">Users</h6>
			<p class="lead mb-4">Datahover may collect and use Users personal information for the following purposes:</p>
			<ul class="mb-4">
				<li>To improve customer service. Information you provide helps us respond to your customer service requests and support needs more efficiently.</li>
				<li>To personalize user experience. We may use information in the aggregate to understand how our Users as a group use the services and resources provided on our Site.</li>
				<li>To improve our Site. We may use feedback you provide to improve our products and services.</li>
				<li>To process payments. We may use the information Users provide about themselves when placing an order only to provide service to that order. We do not share this information with outside parties except to the extent necessary to provide the service.</li>
				<li>To run a promotion, contest, survey or other Site feature.</li>
				<li>To send Users information they agreed to receive about topics we think will be of interest to them.</li>
				<li>To send Users information they agreed to receive about topics we think will be of interest to them.</li>
				<li>To send periodic emails.</li>
				<li>We may use the email address to send User information and updates pertaining to their order.</li>
				<li>It may also be used to respond to their inquiries, questions, and/or other requests.</li>
				<li>If User decides to opt-in to our mailing list, they will receive emails that may include company news, updates, related product or service information, etc. If at any time the User would like to unsubscribe from receiving future emails, we include detailed unsubscribe instructions at the bottom of each email.</li>
			</ul>

			<h6 class="mb-2 fw-bold">Social Media Authors</h6>
			<p class="lead mb-4">The legal basis for the data that we collect and process is pursuant to our legitimate interests in providing our Services to our customers.</p>
			<p class="lead mb-4">Your information is also used in the following ways:</p>
			<ul class="mb-4">
				<li>To allow our customers to learn more about their brand, customers, and competitors;</li>
				<li>To improve our Services.</li>
			</ul>

			<h4 class="mb-2 fw-bold">How we protect your information</h4>
			<p class="lead mb-4">We adopt appropriate data collection, storage and processing practices and security measures to protect against unauthorized access, alteration, disclosure or destruction of your personal information, username, password, transaction information and data stored on our Site.</p>
			<p class="lead mb-4">In compliance with our company’s values and GDPR laws, you will always be immediately notified in the unlikely event of data breach.</p>

			<h4 class="mb-2 fw-bold">Sharing your personal information</h4>
			<p class="lead mb-4">We do not sell, trade, or rent Users’ personal identification information to others. We may share generic aggregated demographic information not linked to any personal identification information regarding visitors and users with our business partners, trusted affiliates and advertisers for the purposes outlined above. We may use third-party service providers to help us operate our business and the Site or administer activities on our behalf, such as sending out newsletters or surveys. We may share your information with these third parties for those limited purposes provided that you have given us your permission.</p>
			<h6 class="mb-2 fw-bold">Social Media Authors</h6>
			<p class="lead mb-4">We share your data with our Customers in the ways outlined in Personal identification information.</p>

			<h4 class="mb-2 fw-bold">Third party websites</h4>
			<p class="lead mb-4">Users may find advertising or other content on our Site that link to the sites and services of our partners, suppliers, advertisers, sponsors, licensors and other third parties. We do not control the content or links that appear on these sites and are not responsible for the practices employed by websites linked to or from our Site. In addition, these sites or services, including their content and links, may be constantly changing. These sites and services may have their own privacy policies and customer service policies. Browsing and interaction on any other website, including websites which have a link to our Site, is subject to that website’s own terms and policies.</p>

			<h4 class="mb-2 fw-bold">Changes to this privacy policy</h4>
			<p class="lead mb-4">Datahover has the discretion to update this privacy policy at any time. When we do, we will revise the updated date at the bottom of this page. We encourage Users to frequently check this page for any changes to stay informed about how we are helping to protect the personal information we collect. You acknowledge and agree that it is your responsibility to review this privacy policy periodically and become aware of modifications.</p>

			<h4 class="mb-2 fw-bold">Your acceptance of these terms</h4>
			<p class="lead mb-4">By using this Site, you signify your acceptance of this policy. If you do not agree to this policy, please do not use our Site. Your continued use of the Site following the posting of changes to this policy will be deemed your acceptance of those changes.</p>

			<h4 class="mb-2 fw-bold">Contacting us</h4>
			<p class="lead mb-2">If you have any questions about this Privacy Policy, the practices of this site, or your dealings with this site, please contact us at: <span class="fw-bold">ETSETRA LTD</span>.</p>
			<p class="lead mb-2">{{ config('etsetra.address') }}</p>
			<p class="lead mb-4"><a class="link-dark fw-bold" href="mailto:{{ config('etsetra.email') }}">{{ config('etsetra.email') }}</a></p>
			<small class="text-muted">This document was last updated on November 27th, 2021</small>
		</div>
	</div>

	@include('includes.footer')
@endsection
