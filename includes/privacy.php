<div class="container">
    <div id="privacyPolicy" class="modal fade" style="display:none" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content modal-round">
                <div class="modal-header">
                    <h2>Privacy Policy</h2>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This server collects data from you in order to determine if you should be allowed in the building and incase 
                        we need to contact you in the future regarding an outbreak in the district.</p>
                    <h4>What data we collect</h4>
                    <p>
                        At the end of the questionnaire this is the data we collect, the following data is collected and stored:
                        <ul>
                            <li>First name</li>
                            <li>Last name</li>
                            <li>Username (Not for Visitors)</li>
                            <li>Email address </li>
                            <li>Phone number </li>
                            <li>True or False if you passed the questionnaire, no symptom information is stored. </li>
                            <li>Date and time form was filled out.</li>
                        </ul>
                    </p>
                    <h4>How we use the data</h4>
                    <p>
                        Data collected through this service is used to:
                        <ul>
                            <li>Allow us to see who has passed or failed the questionnaire.</li>
                            <li>Contact you if an infection was discovered on a day you were in the building.</li>
                        </ul>
                        No personal information is disclosed to third parties.
                    </p>
                    <h4>Your consent</h4>
                    <p>
                        By completing the form you consent to the terms of this privacy policy.
                    </p>
                    <h4>Data removal</h4>
                    <p>
                        Data will be purged from the database after <?php echo $config['purgeafter'];?> days.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>