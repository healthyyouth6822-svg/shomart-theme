<?php
/*
Template Name: Refund & Return Policy
*/

get_header();
?>

<div class="shomart-policy-page">

    <div class="policy-header">
        <h1>↩️ Refund & Return Policy</h1>
        <p>Last updated: <?php echo date('F Y'); ?></p>
    </div>

    <div class="policy-content">

        <div class="policy-section">
            <h2>1. Return Eligibility</h2>
            <p>We accept returns within <strong>4 days</strong> of delivery for the following reasons:</p>
            <ul>
                <li>Item received is damaged or defective</li>
                <li>Wrong item delivered</li>
                <li>Item does not match the description</li>
                <li>Item is missing parts or accessories</li>
            </ul>
        </div>

        <div class="policy-section">
    <h2>Refund Handling Process</h2>
    <p><strong>For orders below ₹2000:</strong></p>
    <ul>
        <li>The customer must first report the issue directly to Shomart via email or WhatsApp.</li>
        <li>Photo evidence of the product and label is required.</li>
        <li>If the issue is verified, the customer will be guided to the assigned shop for resolution.</li>
    </ul>

    <p><strong>For orders above ₹2000:</strong></p>
    <ul>
        <li>The customer must report the issue with proper photo evidence.</li>
        <li>Shomart will coordinate directly with the assigned shop.</li>
        <li>The shop will handle the refund or replacement process.</li>
    </ul>
</div>
<div class="policy-section">
    <h2>Refund Timeline</h2>
    <ul>
        <li>Refund processing time: <strong>5–7 business days</strong>.</li>
        <li>Refund will be issued only after product inspection by the assigned shop.</li>
        <li>If a partner shop repeatedly delays refund processing, 
            Shomart reserves the right to suspend the partnership.</li>
    </ul>
</div>

        <div class="policy-section">
            <h2>3. How to Return</h2>
            <div class="return-steps">
                <div class="return-step">
                    <span class="step-num">1</span>
                    <div>
                        <strong>Contact Us</strong>
                        <p>Email us (shomart.store@gmail.com) within 4 days of receiving your order</p>
                    </div>
                </div>
                <div class="return-step">
                    <span class="step-num">2</span>
                    <div>
                        <strong>Share Details</strong>
                        <p>Provide your order number and photos of the item</p>
                    </div>
                </div>
                <div class="return-step">
                    <span class="step-num">3</span>
                    <div>
                        <strong>Ship Back</strong>
                        <p>We'll arrange pickup( order > Rs 2000) or provide shope details ( order < Rs2000 ) </p>
                    </div>
                </div>
                <div class="return-step">
                    <span class="step-num">4</span>
                    <div>
                        <strong>Get Refund</strong>
                        <p>Refund processed within 5-7 business days</p>
                    </div>
                </div>
            </div>
        </div>
            <div class="policy-section">
    <h2>Damage Claim Policy</h2>
    <p>
        If a product is received damaged or defective, the customer must:
    </p>
    <ul>
        <li>Report the issue within <strong>4 days</strong> of delivery.</li>
        <li>Send clear photos of the product.</li>
        <li>Ensure the product label and serial number are clearly visible.</li>
        <li>Keep original packaging and accessories intact.</li>
    </ul>
    <p>
        We strongly recommend opening the product in the presence of the delivery person.
        Damage claims without proper photo evidence may not be accepted.
    </p>
</div>
        

                <div class="policy-section">
            <h2>5. Order Cancellation Policy</h2>
            <p>
                Orders can be cancelled within <strong>60 minutes</strong> of placing the order.
            </p>

            <ul>
                <li>
                    Cancellation is allowed only before the order status is marked as 
                    <strong>"Out for Delivery"</strong>.
                </li>
                <li>
                    Once the delivery process has started, cancellation is not possible.
                </li>
                <li>
                    If a customer attempts to cancel more than <strong>4 orders within the same day</strong>, 
                    their account may be temporarily restricted for <strong>7 days</strong> to prevent misuse of the platform.
                </li>
                <li>
                    Temporary restrictions are automatically removed after the restriction period ends.
                </li>
            </ul>

            <p style="margin-top:8px;">
                This policy ensures smooth delivery operations and protects our partner shops.
            </p>
        </div>

        <div class="policy-section">
            <h2>6. Contact for Returns</h2>
            <div class="policy-contact">
                <p>📧 Email: shomart.store@gmail.com</p>
                <p>⏰ Response time: Within 24 hours</p>
                <p>📍 Location: Ladakh, India</p>
            </div>
        </div>

    </div>

</div>

<style>
/* Return Steps */
.return-steps {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 10px;
}
.return-step {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.step-num {
    width: 28px;
    height: 28px;
    background: var(--blue);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
}
.return-step strong { font-size: 14px; display: block; margin-bottom: 2px; }
.return-step p { font-size: 12px; color: var(--text-light); margin: 0; }
</style>

<?php get_footer(); ?>