<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pharmacare Company - Solving Healthcare Distribution Challenges</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
section {
margin-top: 10px;
padding: 2px 40px;
margin-bottom: 10px;
}
</style>
</head>
<body>
<?php 
include_once '../commons.php';
echo $header;
?>
  <section id="home">
    <h1>Welcome to the Drug Dispenser</h1>
    <h2>Solving Healthcare Distribution Challenges</h2>
    <a href="#solutions" class="cta-button">Get Started - Explore Our Solutions</a>
  </section>
  
  <section id="about">
    <h2>About Us</h2>
    <p>At our Drug Dispenser, we are committed to revolutionizing the distribution of pharmaceuticals. With our innovative solutions and deep industry expertise, we streamline the supply chain, enhance patient safety, and optimize the pharmaceutical distribution process.</p>
  </section>
  
  <section id="solutions">
    <h2>Our Solutions</h2>
    <ul>
      <li>Efficient patient identification</li>
      <li>Doctor management</li>
      <li>Pharmaceutical company partnerships</li>
      <li>Pharmacy contracts</li>
      <li>Prescription management</li>
    </ul>
  </section>
  
  <section id="testimonials">
    <h2>Testimonials</h2>
    <div class="testimonial">
      <p>"Pharmacare Company has transformed the way we manage our pharmaceutical distribution. Their solutions have significantly improved our supply chain efficiency and patient care."</p>
      <cite>- Omusala David, CEO of GlaskoRemula Pharmaceuticals</cite>
    </div>
    <div class="testimonial">
      <p>"We highly recommend Pharmacare Company to any pharmacy looking to optimize their operations. Their expertise and support have been invaluable in enhancing our prescription processes."</p>
      <cite>- Lamech Munani, Chief Pharmacist at Resilasi Pharmacy</cite>
    </div>
  </section>
  
  <center><h2>Our Services</h2></center>
  <section id="features-showcase">
  <div class="feature-item">
    <img src="../images/prescription.png" alt="Feature 1">
    <h3>Feature 1: Convenient Prescription Management</h3>
    <p>Effortlessly manage your prescriptions online, request refills, and track medication history from the comfort of your home.</p>
  </div>
  <div class="feature-item">
    <img src="../images/rem.png" alt="Feature 2">
    <h3>Feature 2: Extensive Network of Trusted Doctors</h3>
    <p>Access a vast network of experienced doctors specializing in various fields to receive personalized and quality healthcare.</p>
  </div>
  <div class="feature-item">
    <img src="../images/delivery.png" alt="Feature 3">
    <h3>Feature 3: Reliable Medication Delivery</h3>
    <p>Enjoy hassle-free medication delivery to your doorstep, ensuring you never run out of essential medications.</p>
  </div>
  <div class="feature-item">
    <img src="../images/secure.png" alt="Feature 4">
    <h3>Feature 4: Secure and Confidential Services</h3>
    <p>We prioritize your privacy and ensure secure handling of your personal and medical information throughout the entire process.</p>
  </div>
  <div class="feature-item">
    <img src="../images/support.png" alt="Feature 5">
    <h3>Feature 5: Exceptional Customer Support</h3>
    <p>Receive prompt and friendly customer support to address any concerns, answer questions, and provide guidance whenever needed.</p>
  </div>
</section>


  
 <footer>
  <div class="footer-content">
    <div class="footer-section">
      <h3>Contact Us</h3>
      <p>Karen, Nairobi, Kenya</p>
      <p>Email: info@druggy.com</p>
      <p>Phone: +254 473 487 343</p>
    </div>
    <div class="footer-section">
      <h3>Links</h3>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#solutions">Solutions</a></li>
        <li><a href="#pricing">Pricing</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Follow Us</h3>
      <ul class="social-media-icons">
        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2023 Drug Dispenser. All rights reserved.</p>
  </div>
</footer>
</body>
</html>
