=== Booked Add-On: WooCommerce Payments ===
Donate link: https://boxystudio.com/#coffee
Tags: booked, add-on
Requires at least: 4.0
Tested up to: 4.8

Adds the ability to accept payments for appointments using WooCommerce.

== Changelog ==

= 1.4.5 =
* *FIX:* Fixed an issue when clicking the "Pay Now" button.
* *FIX:* Fixed an issue with reminders still being sent for unpaid appointments.

= 1.4.4 =
* **NEW:** Added option to show/hide the product thumbnail in the cart.
* **NEW:** Added support for Booked 2.0.

= 1.4.3 =
* *FIX:* Some bug fixes related to cart items.

= 1.4.2 =
* **NEW** Added support for WordPress 4.8.
* *FIX:* Fixes with lables and values for bookings. Update Booked to the latest version as well or this won't work properly.

= 1.4.1 =
* *FIX:* Fixes an issue with adding appointments to the cart when using WPML.

= 1.4.0 =
* **NEW:** WooCommerce 3.x Support
* **NEW:** The appointment date is now shown in the cart/checkout screens.
* *FIX:* Fixes for "free" products.
* *FIX:* Fixes for product variations.
* *FIX:* Linked thumbnail is now hidden from cart page for appointments.
* *FIX:* Cleaned up the cart/checkout pages in regards to the product display and quantity text.

= 1.3.3 =
* *FIX:* Fixed an issue related to "booked_wc_variables".
* *FIX:* Fixed an issue where the appointment limit (if set) was reached when the user tried to change the appointment date.

= 1.3.2 =
* *FIX:* Fixed a WPML issue where products would not show in different languages where applicable.

= 1.3.1 =
* *FIX:* Fixed an issue where in some cases, the "Change Date" button would not function properly.

= 1.3 =
* *FIX:* Email confirmations will only get sent out when appointments are "completed".
* *NOTE:* Updated to support Booked 1.9 and an upcoming add-on.

= 1.2.23 =
* *FIX:* Fixed a double admin email getting sent out.

= 1.2.22 =
* *FIX:* Fixes the "Change Date" and "Cancel Appointment" buttons. Note that appointments with actual "Paid" orders cannot be cancelled by users.

= 1.2.21 =
* *NEW:* You can now mark manually created appointments as "Paid" from the backend calendar.

= 1.2.20 =
* *FIX:* A few bug fixes to address some Cron related issues.

= 1.2.19 =
* **IMPORTANT CHANGE #1:** Appointments will not be marked as PAID until they are actually Completed or if you mark them as "Completed" from the order screen.
* **IMPORTANT CHANGE #2:** Appointments will not be "Approved" unless you have them set to "Approve Immediately". If you have appointments set to Pending upon creation, they will stay that way until approved by an Administrator or Booking Agent.
* **NOTE:** For those having issues with Paypal payments not getting "Completed", see [this article](https://boxystudio.ticksy.com/article/7575/).

= 1.2.18 =
* *FIX:* Fixes for WordPress 4.5.

= 1.2.17 =
* *FIX:* Fixed an issue where the Payment Selector Button didn't show up after switching calendars in the Settings panel.

= 1.2.16 =
* *FIX:* Fixes for Booked 1.7.9.

= 1.2.15 =
* *FIX:* Adjustments to work with Booked 1.7.8.

= 1.2.14 =
* **NEW:** Product variations now show up in the cart/checkout, etc.

= 1.2.13 =
* **NEW:** Added an option to choose between redirecting to the Checkout page (default) or to the Cart instead.

= 1.2.12 =
* *FIX:* Added support for the Booked 1.7.0 release.
* *FIX:* Fixed an issue where the "Change Date" and/or "Add to Google" buttons wouldn't show up.

= 1.2.11 =
* *FIX:* Added a fix for the '%name%' token in the admin notification email.

= 1.2.10 =
* *FIX:* Added a fix to support approval emails getting sent out when an order is complete.
* *FIX:* Added some new "actions" to support the upcoming "Twilio SMS" Add-On.

= 1.2.05 =
* *FIX:* Booking payments will now be marked as approved, even when in the "Processing" state.
* *FIX:* Fixed a "Custom Fields" sorting issue.

= 1.2.04 =
* *NEW:* Appointment form will now properly display pricing in accordance with your WooCommerce currency settings.

= 1.2.03 =
* *NEW:* New plugin update server has been implemented.

= 1.2.02 =
* *FIX:* Quick fix for some profiles that were not showing the "Change Date" and/or "Pay" buttons under the appointments.

= 1.2.01 =
* *NEW:* Ready for Booked 1.6.x
* *NEW:* With Booked 1.6.x, you can now add a WooCommerce product field to each calendar individually.
* Some other minor bug fixes throughout.

= 1.1.0 =
* *NEW:* Calendar Name (if applicable) will show up with the order.
* *NEW:* Booking Agent's name (if applicable) will show up with the order.
* *FIX:* Fixed issues with variable products.
* *FIX:* Fixed an issue where PHP errors would show up in the cart/checkout pages if an appointment was missing.

= 1.0.7 =
* *FIX:* Got rid of the big red message, it was causing more issues than not.

= 1.0.6 =
* *FIX:* Added support for the "Booked Front-end Agents" add-on.

= 1.0.5 =
* *FIX:* Fixed an issue with language files not loading from the /wp-content/languages/plugins/ folder.
* *FIX:* Fixed some language issues with the Custom Fields builder page.
* *FIX:* Fixed a Javascript error preventing certain plugins from running.

= 1.0.4 =
* *FIX:* Added a red message to the top of appointment products on the front-end for more clarity.
* *FIX:* Fixed language translation issues.
* *FIX:* Fixed an issue where this plugin would show up twice in the plugins list.

= 1.0.3 =
* *FIX:* Fixed an issue where the "Change Date" button would show up on non-paid appointments.
* *FIX:* Fixed an issue where the "Change Date" button would put the appointment back into the cart upon changing the date.

= 1.0.2 =
* *FIX:* Fixed an issue where the cart would be empty when booking an appointment.

= 1.0.1 =
* *FIX:* Confirmation emails are now sent to the customer for WooCommerce-based appointments.

= 1.0.0 =
* Initial Release!