<mvc:View
    controllerName="io.pragmatiqu.dashboard.controller.Dashboard"
    displayBlock="true"
    xmlns="sap.m"
    xmlns:f="sap.f"
    xmlns:cards="sap.f.cards"
    xmlns:w="sap.ui.integration.widgets"
    xmlns:mvc="sap.ui.core.mvc"
>
    <App id="app">
        <Page
            title="{i18n>appTitle}"
            id="page">
            <content>

                <!--VBox>
                    <x-ui5-element id="io.pragmatiqu.offers.tiles.pending" />
                    <x-ui5-element id="io.pragmatiqu.offers.tiles.asking" />
                </VBox-->
                <f:GridContainer class="sapUiResponsiveMargin">
                    <f:layout>
                        <f:GridContainerSettings rowSize="84px" columnSize="84px" gap="8px" />
                    </f:layout>
                    <f:layoutXS>
                        <f:GridContainerSettings rowSize="70px" columnSize="70px" gap="8px" />
                    </f:layoutXS>
                    <GenericTile header="Country-Specific Profit Margin"
                                 frameType = "OneByOne" subheader="Expenses" press="onPress">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="EUR" footer="Current Quarter" >
                            <NumericContent scale="M" value="1.96" valueColor="Error" indicator="Up" withMargin="false" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="US Profit Margin" press="onPress" frameType = "OneByOne">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="Unit">
                            <NumericContent scale="%" value="12" valueColor="Critical" indicator="Up" withMargin="false" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Sales Fulfillment Application Title"
                                 subheader="Subtitle" press="onPress" frameType = "OneByOne">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="EUR" footer="Current Quarter">
                            <ImageContent src="sap-icon://home-share" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Manage Activity Master Data Type"
                                 subheader="Subtitle" press="onPress" frameType = "OneByOne">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent>
                            <ImageContent src="https://sdk.openui5.org/test-resources/sap/m/demokit/sample/GenericTileAsLaunchTile/images/SAPLogoLargeTile_28px_height.png" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Manage Activity Master Data Type With a Long Title Without an Icon"
                                 subheader="Subtitle Launch Tile" mode="HeaderMode" press="onPress">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="EUR" footer="Current Quarter" />
                    </GenericTile>

                    <GenericTile header="Jessica D. Prince Senior Consultant"
                                 subheader="Department" press="onPress">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent>
                            <ImageContent src="https://sdk.openui5.org/test-resources/sap/m/demokit/sample/GenericTileAsLaunchTile/images/ProfileImage_LargeGenTile.png" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile backgroundImage="https://sdk.openui5.org/test-resources/sap/m/images/NewsImage1.png"
                                 frameType="TwoByOne" press="onPress">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="3" />
                        </layoutData>
                        <TileContent footer="Report Available" frameType="OneByOne">
                            <NewsContent
                                contentText="Realtime Business Service Analytics"
                                subheader="SAP Analytics Cloud" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile backgroundImage="https://sdk.openui5.org/test-resources/sap/m/demokit/sample/SlideTile/images/NewsImage1.png"
                                 frameType="TwoByOne" press="onPress">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="4" />
                        </layoutData>
                        <TileContent footer="August 21, 2016">
                            <NewsContent
                                contentText="Wind Map: Monitoring Real-Time and Forecasted Wind Conditions across the Globe"
                                subheader="Today, SAP News" />
                        </TileContent>
                    </GenericTile>

                    <SlideTile transitionTime="250" displayTime="2500">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="4" />
                        </layoutData>
                        <GenericTile
                            backgroundImage="https://sdk.openui5.org/test-resources/sap/m/demokit/sample/SlideTile/images/NewsImage1.png"
                            frameType="TwoByOne" press="onPress">
                            <TileContent footer="August 21, 2016">
                                <NewsContent
                                    contentText="Wind Map: Monitoring Real-Time and Forecasted Wind Conditions across the Globe"
                                    subheader="Today, SAP News" />
                            </TileContent>
                        </GenericTile>
                        <GenericTile
                            backgroundImage="https://sdk.openui5.org/test-resources/sap/m/demokit/sample/SlideTile/images/NewsImage2.png"
                            frameType="TwoByOne" state="Failed">
                            <layoutData>
                                <f:GridContainerItemLayoutData minRows="2" columns="4" />
                            </layoutData>
                            <TileContent footer="August 21, 2016">
                                <NewsContent
                                    contentText="SAP Unveils Powerful New Player Comparision Tool Exclusively on NFL.com"
                                    subheader="Today, SAP News" />
                            </TileContent>
                        </GenericTile>
                    </SlideTile>

                    <GenericTile header="Country-Specific Profit Margin"
                                 subheader="Expenses" press="onPress" systemInfo = "system info" appShortcut = "app shortcut">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent  unit="EUR" footer="Current Quarter">
                            <NumericContent scale="M" value="1.96" valueColor="Error" indicator="Up" withMargin="false" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Feed Tile that shows updates of the last feeds given to a specific topic:"
                                 frameType="OneByOne" press="onPress">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent footer="New Notifications">
                            <FeedContent contentText="@@notify Great outcome of the Presentation today. New functionality well received."
                                         subheader="About 1 minute ago in Computer Market" value="352" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Country-Specific Profit Margin"  press="onPress"
                                 frameType="OneByOne">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="EUR" footer="Current Quarter">
                            <NumericContent scale="M" value="1.96" valueColor="Error" indicator="Up" withMargin="false" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Cumulative Totals" subheader="Expenses" press="onPress" frameType="OneByHalf">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="Unit" footer="Footer Text">
                            <NumericContent value="1762" icon="sap-icon://line-charts" withMargin="false" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Right click to open in new tab"
                                 subheader="Link tile" press="onPress" url="https://www.sap.com/" frameType="OneByHalf">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent>
                            <ImageContent src="https://sdk.openui5.org/test-resources/sap/m/demokit/sample/GenericTileAsLaunchTile/images/SAPLogoLargeTile_28px_height.png" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="US Profit Margin" press="onPress" >
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="Unit">
                            <NumericContent scale="%" value="12" valueColor="Critical" indicator="Up" withMargin="false" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Sales Fulfillment Application Title"
                                 subheader="Subtitle" press="onPress" systemInfo = "system" appShortcut = "shortcut">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="EUR" footer="Current Quarter">
                            <ImageContent src="sap-icon://home-share" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Cumulative Totals" subheader="Expenses" press="onPress" >
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent unit="Unit" footer="Footer Text">
                            <NumericContent value="1762" icon="sap-icon://line-charts" withMargin="false" />
                        </TileContent>
                    </GenericTile>

                    <GenericTile header="Right click to open in new tab"
                                 subheader="Link tile" press="onPress" url="https://www.sap.com/" frameType="OneByHalf">
                        <layoutData>
                            <f:GridContainerItemLayoutData minRows="2" columns="2" />
                        </layoutData>
                        <TileContent>
                            <ImageContent src="https://sdk.openui5.org/test-resources/sap/m/demokit/sample/GenericTileAsLaunchTile/images/SAPLogoLargeTile_28px_height.png" />
                        </TileContent>
                    </GenericTile>
                </f:GridContainer>

            </content>
        </Page>
    </App>
</mvc:View>
