<?php
session_start();
require( 'manager/includes/pdo.php' );
/*require( 'manager/includes/check_login.php' );*/
$msg = '';
$err = '';

if ( isset( $_POST['submit'] ) ) {
  $sql = "UPDATE users SET last_login = NOW(), agree_to_terms = 1 WHERE id = ?";
  $conn->exec( $sql, array( $_SESSION['user_id'] ) );
  $sql = 'INSERT INTO activity_log SET date_created = NOW(), user_id = ?, activity_type_id = 12, ip_address = ?';
  $conn->exec( $sql, array( $_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ) );
  header( 'Location: main.php' );
}
$language_bar   =   true;
include_once 'header-login.php';
?>

<div class="avo_comm_login">
    <div class="container">
        <img src="images/login_pg_logo.png" alt="" class="login_avo_comm_img"/>
        <h2>AGREE TO TERMS</h2>
        <form action="terms.php" method="POST" onSubmit="return validateForm(this);">
            <div class="terms-condition">
              <h3>Avo Communicator Legal</h3>
            <h4>Terms of Use Agreement</h4>
          <p>
              THIS TERMS OF USE AGREEMENT GOVERNS YOUR ACCESS TO AND USE OF THE AVO COMMUNICATOR PORTAL. BY CLICKING THE BOX INDICATING YOUR ACCEPTANCE OF THIS AGREEMENT, YOU ACKNOWLEDGE THAT YOU HAVE READ, UNDERSTOOD, AND AGREE TO BE BOUND BY THE TERMS AND CONDITIONS OF THIS AGREEMENT. BECAUSE THIS AGREEMENT IS SUBJECT TO CHANGE AND YOU WILL BE BOUND BY SUCH CHANGES, YOU SHOULD PERIODICALLY REVIEW THIS AGREEMENT. IF YOU DO NOT AGREE WITH THESE TERMS AND CONDITIONS, YOU MUST NOT ACCEPT THIS AGREEMENT AND YOU MAY NOT ACCESS OR USE THE AVO COMMUNICATOR PORTAL.
          </p>
<h5>1. SCOPE</h5>
          <p>
              Avocados From Mexico (“AFM”) maintains for its stakeholders (“End Users”) a data viewing website, or Avo Communicator Portal, including related features, information, and content. AFM may provide access for You to Your Portal Content and Your Portal pursuant to the terms and conditions of this Terms of Use Agreement (“Agreement”). This Agreement governs the use of the Avo Communicator Portals and AFM content. You may only use the Avo Communicator Portals and the Avocado From Mexico content in accordance with the terms of this Agreement. This Agreement is effective as of the date that You first accept this Agreement by logging in to the Avo Communicator Portal.
          </p>
            <h5>2. DEFINITIONS</h5>
            <h6>Affiliate</h6>
          <p>
              shall mean, with respect to a Party, a person or entity that directly or indirectly through one or more intermediaries, controls, is controlled by or is under common control with that Party.
          </p>
            <h6>AFM Content</h6>
          <p>
              shall mean all text, images, graphics, photographs, video clips, designs, icons, sounds, information, data, and other materials displayed on, contained within, or otherwise associated with the Avo Communicator Portals. AFM Content may include, but is not limited to, information and metrics concerning its trade and shopper marketing programs including information related to the distribution of point of sales and marketing materials.
          </p>
            <h6>Avo Communicator Portal</h6>
            <p>shall mean a separate data viewing website, including related features, information and content maintained by AFM for certain End Users.</p>
<h6>AFM Technology</h6>
            <p>shall mean all methods, methodologies, procedures, processes, know how, software, algorithms, techniques, and other technology displayed on, used in creating, compiling, or running, or otherwise incorporated into the Avo Communicator Portal.</p>
            <h6>Party and Parties</h6>
            <p>shall mean one or both, respectively, of You, (an End User), and AFM</p>
            <h6>Portal</h6>
            <p>shall mean a data viewing website, including related features, products, and services, maintained by AFM for certain End Users.</p>
            <h6>Third Parties</h6>
            <p>shall mean End User clients or customers.</p>

            <h6>User Content</h6>
            <p>shall mean data, images, or other information placed on the portal by AFM and consumed by End User.</p>
            <h6>You or Your</h6>
            <p>shall refer to the End User agreeing to this Agreement.</p>
            <h6>Your Portal</h6>
            <p>shall mean a data viewing website, including related features, information, and content, maintained by AFM for You, and which contains Content.</p>
            <h6>Your Portal Content</h6>
            <p>shall mean information from the AFM Content that pertains to You and is available for viewing by You in Your Portal. Your Portal Content does not include AFM Content of other End Users. Your Portal Content may include, but is not limited to, information and metrics concerning AFM’s trade and shopper marketing programs</p>
            <h5>3. CHANGES IN THE AGREEMENT AND/OR PORTAL</h5>
            <p><strong>3.1 Changes in this Agreement.</strong> AFM reserves the right, in its sole discretion, to modify, add or remove any portion of this Agreement, in whole or in part, at any time with prior notice to You</p>
            <p><strong>3.2 Changes to Your Portal.</strong> Your Portal may be modified, revised or upgraded from time to time by AFM without notice or liability. AFM may change, suspend, or discontinue any aspect of Your Portal at any time, including, but not limited to: Your Portal Content, features and information offered, hours of availability, and equipment or software needed for access or use of Your Portal. AFM may also impose limits on certain features of Your Portal and/or restrict Your access to parts or all of Your Portal without notice or liability.</p>
            <h5>4. USE OF PORTAL; PORTAL CONTENT; TRADEMARKS; INTELLECTUAL PROPERTY RIGHTS</h5>
            <p>
              <strong>4.1 Permissible Use.</strong> You and Your authorized employees may access, download, and use material displayed on Your Portal for internal business use only in accordance with the terms of this Agreement. You may not make derivative works, distribute, modify or otherwise use Your Portal Content for public or non-business purposes without prior written permission from AFM. Your Portal contains names, logos, trademarks, service marks and other intellectual property which may not be used by You for any purpose without prior written permission from AFM
            </p>
            <p>
                <strong>4.2 Access.</strong> Access to Your Portal is granted in the absolute discretion of AFM , and may be terminated at any time. AFM will grant access to Your Portal upon receiving a list of names and email addresses for each End User from your business. Access is permitted by user name and password only and each End User must have his/her own unique user name. You are responsible for protecting the confidentiality of all user names and passwords registered to Your account, and You accept responsibility for all actions which occur under such names and/or passwords. If You believe that Your user name or password has been compromised, You must contact AFM promptly.
            </p>
            <p>
              <strong>4.3 Accuracy.</strong> While AFM makes the best efforts to provide accurate and updated information through Your Portal, You acknowledge that Your Portal Information may contain errors or omissions or may not be current.
            </p>
            <p>
              <strong>4.4 Confidentiality.</strong> You understand that You may receive certain Confidential Information (as defined below) from AFM including information about third parties. “Confidential Information” includes Your Portal, Your Portal Content, trade secrets, proprietary rights, financial, sales and marketing data and any other information transmitted via Your Portal. You agree that you will not disclose Confidential Information to any third party. You agree not to use or reproduce such Confidential Information without the prior written consent of AFM except as otherwise permitted herein. You agree that You will limit access to Confidential Information to those of Your employees who have a need to know such Confidential Information. You agree that upon written request You will return to AFM any and all written or tangible materials (including all copies) of Confidential Information in Your possession. You acknowledge that the Confidential Information is of a special, unique and extraordinary character, and that a breach of this Agreement by You will cause continuing and irreparable injury to AFM and its third parties for which monetary damages would not be an adequate remedy. In the event of a breach of this Agreement, in addition to any other legal remedies available, AFM and its Third Parties have the right to seek injunctive or other equitable relief without any requirement for the posting of any security or bond.
            </p>
            <p>
              <strong>4.5 Lawful Use; Portal Content.</strong> Your access to and use of Your Portal is subject to all applicable international, federal, state and local laws and regulations. You represent and warrant that You will not use Your Portal in any manner or for any purposes that are unlawful or prohibited by this Agreement. AFM may immediately suspend or terminate Your access to Your Portal if it determines that Your use is or may be unlawful.
The computer servers storing Your Portal Content are located in the United States. You may not transfer any data onto Your Portal from another country if to do so would violate any privacy, data transfer, or data protection laws of the United States or such other country. You agree that You are responsible for complying with applicable privacy,
data transfer, and data security laws, and You agree to abide by such laws in connection with Your use of Your Portal.
You may use Your Portal Content and the AFM Technology only for Your own internal and informational purposes, and You may not copy, modify, reverse-engineer, translate, disassemble, or decompile any of the AFM Technology. You may not rent or lease the AFM Technology or use thereof. You may not electronically transfer copies of Your Portal Content or any other data available on Your Portal without the express written consent of AFM. You may not distribute, publish, transmit, modify, create derivative works from, or in any way exploit, any of Your Portal Content and the AFM Technology, in whole or in part, for any purpose. You may not remove or alter any copyright or other notices on any copy of Your Portal Content or AFM Technology. You may not transfer, share, outsource, or distribute copies of Your Portal Content or AFM Technology to third parties. Nothing in this Agreement shall be construed as granting any permission (except as set forth in this section), right, or license in any of the AFM Content or AFM Technology. Nothing in this Agreement shall be construed as granting any right of access to AFM Technology except as expressly set forth in this Agreement.
You agree to refrain from placing on Your Portal: (i) any offensive or unlawful material; (ii) any material unrelated to the services that AFM has been engaged to perform for You; (iii) any material containing any computer virus, worm, or other malicious code; and (iv) any material that infringes another person’s copyright, trade or service mark, patent, or other property right.
Please remember that Your Portal is designed to facilitate the exchange of information between AFM and You. AFM may delete all or a portion of Your Portal Content or User Content, consistent with AFM’s document retention policies or otherwise, at any time. Although AFM may endeavor to provide You with advance notice of the deletion of all or a portion of Your Portal Content, You should not assume that AFM will do so, and You should not consider Your Portal to be a storage, archival, or back-up location or tool with respect to any of Your Portal Content or other information associated with Your Portal.
You agree not to use any device, software or routine to interfere or attempt to interfere with the proper working of the Avo Communicator Portal, AFM Content, or AFM Technology or any information being communicated on the Avo Communicator Portal or to affect the images or data or access of any End User. You may not take any action that imposes an unreasonable or disproportionately large load on the AFM Technology, or affects the ability of any other End User to use its Portal.
            </p>
            <p>
              <strong>4.6 Trademarks.</strong> The trademarks, service marks, and logos used and displayed on the Portals are trademarks of AFM and other third parties. Elements of Your Portal are protected by copyright, trade dress and other laws and may not be copied or imitated, in whole or in part. No right or license to use any trademark, service mark, logo, graphic, sound, image, or other aspect of Your Portal is granted by this Agreement.
            </p>
            <p>
              <strong>4.7 Intellectual Property Rights.</strong> All intellectual property rights are fully reserved by AFM, its Clients and any third-party owners of those rights. Without limiting the foregoing, unless otherwise noted all text, images, graphics, photographs, video clips, designs, icons, sounds, information (including Your Portal Content), data, and other materials and all methods, methodologies, procedures, processes, know-how, software, algorithms, techniques, and other technology displayed, used, or incorporated on Your Portal are copyrights, trademarks, service marks, trade secrets, or other intellectual property or proprietary content owned by AFM.
            </p>
            <h5>5. DISCLAIMER OF WARRANTIES</h5>
            <p>
              <strong>5.1 General Disclaimer.</strong> ALL PRODUCTS, SERVICES, INFORMATION, AFM CONTENT, TEXT, AND RELATED GRAPHICS CONTAINED WITHIN OR AVAILABLE THROUGH YOUR PORTAL ARE PROVIDED TO YOU ON AN “AS IS” AND “AS AVAILABLE” BASIS. AFM MAKES NO REPRESENTATIONS OR WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, AS TO THE OPERATION OF YOUR PORTAL, THE AFM TECHNOLOGY, SOFTWARE, MAINTENANCE SERVICES, SUPPORT SERVICES, DELIVERABLES, RESOURCES, EQUIPMENT, AFM CONTENT, OR OTHER ITEMS OR SERVICES PROVIDED BY AFM UNDER THIS AGREEMENT OR THE RESULTS TO BE DERIVED FROM THE USE THEREOF. WITHOUT LIMITING THE FOREGOING, AFM DOES NOT WARRANT OR REPRESENT THAT YOUR PORTAL WILL OPERATE ERROR-FREE OR UNINTERRUPTED, THAT DEFECTS WILL BE CORRECTED, THAT YOUR PORTAL AND ITS SERVERS WILL BE FREE OF VIRUSES AND OTHER HARMFUL COMPONENTS, OR THAT YOUR PORTAL CONTENT WILL BE ACCURATE, COMPLETE, RELIABLE, CURRENT, OR ERROR-FREE. TO THE FULLEST EXTENT PERMISSIBLE PURSUANT TO APPLICABLE LAW, AFM DISCLAIMS ALL REPRESENTATIONS AND WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF (i) MERCHANTABILITY OR SATISFACTORY QUALITY, (ii) FITNESS FOR A PARTICULAR PURPOSE, (iii) TITLE, AND (iv) NON-INFRINGEMENT OF THE RIGHTS OF THIRD PARTIES, AND ANY WARRANTIES ARISING FROM COURSE OF DEALING, USAGE OF TRADE, OR COURSE OF PERFORMANCE WITHOUT LIMITING THE ABOVE, AFM SHALL NOT BE RESPONSIBLE FOR AND SPECIFICALLY DISCLAIMS ALL WARRANTY OBLIGATIONS WHATSOEVER WITH RESPECT TO YOUR NEGLIGENCE OR MISUSE OF YOUR PORTAL, COMPUTER HARDWARE, OR THIRD-PARTY SOFTWARE MALFUNCTIONS, NONCOMPLIANT DATA FORMATS, DATA INPUT ERRORS, OR YOUR FAILURE TO FOLLOW INSTALLATION AND OPERATING INSTRUCTIONS PROVIDED BY AFM. YOU EXPRESSLY AGREE THAT YOUR USE OF YOUR PORTAL, INCLUDING ALL DATA OR CONTENT VIEWED OR TRANSMITTED THROUGH YOUR PORTAL, IS AT YOUR SOLE RISK.
            </p>
            <p>
              <strong>5.2 Limitation of Remedies.</strong> In the event Your Portal fails to function properly in accordance with the applicable documentation, AFM shall use commercially reasonable efforts to repair or replace (at AFM option) the AFM Technology for such Portal with
other technology that functions properly, or in the case of errors in the technology documentation, to correct the documentation so that it correctly represents the performance of the AFM Technology. Notwithstanding anything in this Agreement to the contrary, if and to the extent that any part of Your Portal is owned by a third party and licensed to AFM for distribution, You agree (a) to be bound by any terms and conditions of use required by such third party and (b) to look solely to such third party for any warranty concerning such part of Your Portal. THIS SECTION 6.2 SETS FORTH YOUR SOLE REMEDY FOR ANY BREACH OF ANY WARRANTY BY AFM REGARDING YOUR PORTAL AND THE AFM TECHNOLOGY.
            </p>
            <h5>6. LIMITATIONS ON LIABILITY</h5>
            <p>
              Circumstances may arise when, because of a default on the part of AFM, or other liability, You may be entitled to recover damages from AFM. In each such instance, regardless of the basis on which You are entitled to claim damages from AFM, AFM will only be liable for bodily injury (including death) and/or damage to Your real property caused by AFM. In no event shall AFM, its agents, licensors, or service providers, or any other person or entity involved in creating, promoting, maintaining, hosting, or otherwise making available any of the AFM Content, Technology, or other aspect of the Avo Communicator Portal, be liable to You or any other person or entity for any indirect, incidental, special, consequential, punitive, or other such damages, including, without limitation, lost profits or lost revenues, even if advised of the possibility of such damages, including but not limited to any damages associated with: (i) loss of goodwill, profits, Your Portal Content, or other data, or other such losses; (ii) Your use or inability to use Your Portal, any unauthorized use of Your Portal, or any function of Your Portal or failure of Your Portal to function; (iii) any use of information pertaining to You or Your business that is accessed or used by third parties accessing Your Portal; (iv) Your reliance on Your Portal Content; (v) damage to your computer equipment or other property on account of your access to or use of Your Portal or your downloading of information from Your Portal; (vi) the provision of or failure to provide any service through Your Portal; (vii) errors or inaccuracies in the AFM Content, Technology, or any advertising or other information, software, products, services, and related graphics used, viewed, or obtained through Your Portal; or (viii) any property loss including damage to Your computer or computer system caused by viruses or other malicious code encountered during or on account of access to or use of Your Portal or any third-party website linked to Your Portal. These limitations of liability shall apply regardless of the form of action, whether based in contract, negligence, strict liability, other tort, or otherwise, and even if AFM have been advised of the possibility of any particular damages. You hereby release AFM from all obligations, liability, claims, or demands in excess of this limitation. The Parties acknowledge that each of them relied upon the inclusion of this limitation in consideration of entering into this Agreement. AFM’S ENTIRE LIABILITY IS SET FORTH IN THIS SECTION 6.
            </p>
            <h5>7. INDEMNIFICATION</h5>
            <p>YOU AGREE TO INDEMNIFY, DEFEND, AND HOLD HARMLESS AFM, ITS AGENTS, LICENSORS, CLIENTS AND SERVICE PROVIDERS, AND THEIR RESPECTIVE AFFILIATES AND SUBSIDIARIES, AND THEIR PAST AND PRESENT OFFICERS, DIRECTORS, EMPLOYEES, REPRESENTATIVES, AGENTS, SUCCESSORS, AND ASSIGNS, FROM AND AGAINST ANY AND ALL CLAIMS, ACTIONS, DEMANDS, LIABILITIES, COSTS, AND EXPENSES, INCLUDING, WITHOUT LIMITATION, REASONABLE ATTORNEYS’ FEES, RESULTING FROM YOUR (INCLUDING YOUR EMPLOYEES AND AGENTS OR ANYONE USING YOUR USER NAMES OR PASSWORDS) (1) BREACH OF ANY PROVISION OF THIS AGREEMENT, INCLUDING ANY WARRANTY YOU PROVIDE HEREIN, (2) NEGLIGENCE OR INTENTIONAL MISCONDUCT, (3) TRANSMISSION OF ANY VIRUSES, TROJAN HORSES OR OTHER HARMFUL BUGS OR PROGRAMS, OR (4) OTHERWISE RESULTING IN ANY WAY FROM YOUR USE OF YOUR PORTAL. WITHOUT LIMITING THE FOREGOING, YOU AGREE TO INDEMNIFY AND HOLD AFM HARMLESS FROM ANY COSTS OR EXPENSES THAT AFM MAY SUSTAIN AS A RESULT OF THE PRIVACY, DATA TRANSFER, AND DATA PROTECTION LAWS OF THE UNITED STATES OR ANY OTHER COUNTRY, WITH RESPECT TO ANY USER CONTENT PLACED ON YOUR PORTAL. YOU HEREBY RELEASE AND DISCHARGE AFM FROM ALL CLAIMS, DEMANDS AND CAUSES OF ACTION, WHETHER KNOWN OR UNKNOWN, ARISING OUT OF OR RELATED TO ANY UNAUTHORIZED ACCESS TO OR USE OF ANY AVO COMMUNICATOR PORTAL OR ANY INACCURACY, ERRORS OR OMISSIONS CONTAINED IN ANY AVO COMMUNICATOR PORTAL OR ANY INFORMATION CONTAINED IN OR DISPLAYED THROUGH THE AVO COMMUNICATOR PORTAL.</p>
            <h5>8. HOW TO CONTACT AFM</h5>
            <p>Please address correspondence concerning Your Portal to: AFM, 222 WEST LAS COLINAS BLVD, SUITE 850 E, IRVING, TEXAS 75039</p>

             <h5>9. ACCESS TO YOUR PORTAL</h5> 
             <p>You will be provided with a username and password to access Your Portal. You should take measures to maintain and preserve the confidentiality of Your username and password associated with Your Portal. You agree not to disclose to or share Your username or password with any third parties or use Your username or password for any unauthorized purposes. You are solely responsible for any liability or damages resulting from any failure to maintain the confidentiality of Your username and password and AFM shall not be liable for any losses that may result from any unauthorized use of Your Portal or failure to maintain appropriate confidentiality measures. You are also solely and fully responsible and liable for all activities that occur under Your Portal account, or using Your username and password. You agree to immediately notify AFM if You suspect any breach of security such as loss, theft, or unauthorized disclosure or use of any username or password. You agree to exit from Your Portal at the end of each session and to change Your password as required by AFM. You agree to promptly notify AFM if You are terminated or resign from Your current employment position and
are no longer authorized to access Your Portal so that Your access to Your Portal may be disabled.</p>
            <h5>10. TERM; TERMINATION</h5>
            <p><strong>10.1 Ceasing Operations; Termination.</strong></p>
            <p><strong>10.1.1 Termination.</strong>This Agreement may be terminated as provided herein.</p>
            <p><strong>10.1.2 Ceasing Operations.</strong>AFM shall not have any ongoing obligation to provide the Avo Communicator Portal; thus, AFM may cease to operate the Avo Communicator Portal at any time and for any reason. Without limiting the foregoing, AFM may cease to provide Your Portal in the event of: (i) any dispute or termination of AFM’S relationship with You (ii) any dispute concerning ownership or control of Your Portal account; (iii) use of Your Portal account in a manner that AFM, in its sole discretion, considers improper or unacceptable, or (iv) any violation by You of the terms of this Agreement. AFM reserves the right to limit the period of time during which Your Portal Content is available on Your Portal. Your Portal should not be viewed as Your backup, archival, or storage service with respect to any User Content or AFM Content.</p>

            <p><strong>10.2.1 Return of Software and Documentation.</strong>Within 30 days of termination for any reason, You shall return to AFM, or destroy all copies of, any AFM Technology, Content, or confidential information residing on Your computers or in printed form, that are in Your possession or control and, if requested by AFM, shall certify the return or destruction of the same in writing within three (3) days of such a request.</p>

            <h5>11. GENERAL PROVISIONS.</h5>
            <p><strong>11.1 Entire Agreement.</strong>This Agreement embodies the entire agreement and understanding between the Parties hereto relating to the subject matter hereof and supersedes any prior agreements and understandings relating to the subject matter hereof. This Agreement in no way alters the terms of other agreements not relating to the subject matter hereof between AFM and End Users.</p>

            <p><strong>11.2 Binding Effect.</strong>This Agreement shall be binding upon, and shall inure to the benefit of, the Parties hereto, and their respective successors and permitted assigns.</p>

            <p><strong>11.3 Assignability.</strong>AFM may assign this Agreement and its rights and obligations hereunder in its sole discretion. You may not assign this Agreement or the rights or obligations hereunder without the prior written consent of AFM. If a change of control of Your business or a sale of substantially all Your business’s assets is intended to include an assignment of this Agreement, such assignment shall require the prior written consent of AFM.
A change of control of Your business or a sale of substantially all Your business’s assets shall constitute an assignment requiring AFM’s prior written consent. You shall
provide notice to AFM promptly following any change in control. The term “change in control” as used in this paragraph refers to a transaction or series of related transactions in which fifty percent (50%) or more of Your voting securities or the voting securities of Your direct or indirect parent are transferred to any person or group of affiliated persons.</p>

<p><strong>11.4 Severability.</strong>If any part or provision of this Agreement is or shall be deemed violative of any applicable laws, rules or regulations, such legal invalidity shall not void this Agreement or affect the remaining terms and provisions of this Agreement, and this Agreement shall be construed and interpreted to comport with all such laws, rules, or regulations to the maximum extent possible.</p>

<p><strong>11.5 Force Majeure.</strong>AFM shall not be liable for any delay in performance or any failure in performance hereunder caused in whole or in part by reason of force majeure, which shall be deemed to include the occurrence of any event beyond the control of AFM, including without limitation war (whether an actual declaration thereof is made or not), sabotage, insurrection, riot and other acts of civil disobedience, action of a public enemy, failure or delays in transportation, laws, regulations or acts of any national, state or local government (or any agency, subdivision or instrumentality thereof), judicial action, labor dispute, accident, fire, explosion, flood, storm or other act of God, shortage of labor, fuel, raw materials, machinery or technical failures.</p>

<p><strong>11.6 Costs of Suit; Governing Law; Injunctive Relief.</strong></p>

<p><strong>11.7 Costs of Suit.</strong>If either Party brings any action for relief against the other, declaratory or otherwise, the losing Party shall pay the successful Party a reasonable sum for legal fees and expenses in such action.</p>
<p><strong>11.8. Applicable Law, Jurisdiction and Venue.</strong>This Agreement shall be governed by the laws of the District of Colombia without giving effect to its conflict of laws rules. The Parties submit to the exclusive jurisdiction of the courts located in the District of Colombia for the resolution of any disputes between the Parties.</p>
<p><strong>11.9 Equitable Relief.</strong>The Parties acknowledge and agree that any breach of that Party’s obligations hereunder may cause the other Party irreparable injury for which there are no adequate remedies at law and that the other Party shall be entitled to equitable relief in addition to other remedies available to it.</p>
<p><strong>11.10 Waivers.</strong>The terms of this Agreement may be waived by, and only by, a written instrument executed by the Party against whom such waiver is sought to be enforced.</p>

<p><strong>11.11 Headings; Certain Terms.</strong>The headings in this Agreement are inserted merely for the purpose of convenience and shall not affect the meaning or interpretation of this Agreement. The words “include”, “includes” and “including” do not connote limitation in any way. Any reference to “writing” or “written” includes fax and email.</p>

<p><strong>11.12 No Third Party Beneficiaries.</strong>The terms and conditions of this Agreement, express or implied, exist only for the benefit of the Parties to this Agreement and their respective successors and permitted assigns. No other person or entity will be deemed to be a third party beneficiary of this Agreement; provided, however, AFM has the right to enforce the terms of this Agreement to the extent it pertains to protection of confidential information or other proprietary rights of its third parties.</p>

<p><strong>11.13 No Implied Rights or Remedies.</strong>Except as otherwise expressly provided herein, nothing herein expressed or implied is intended or shall be construed to confer upon or to give any person, firm, or corporation, other than the Parties hereto and their respective successors and assigns, any rights or remedies under or by reason of this Agreement.</p>



            </div>
            <input type="submit" name="submit" value="I Agree" />
          <a href="/logout.php"><input type="button" name="decline" value="I Decline" /></a>
        </form>
        <img src="images/login_footer_img.png" alt="" class="login_ftr_img" />
    </div>
</div>

<?php
include_once 'footer-login.php';
?>