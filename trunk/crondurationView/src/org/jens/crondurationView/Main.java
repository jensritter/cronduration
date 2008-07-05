package org.jens.crondurationView;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.GradientPaint;

import javax.swing.UIManager;
import javax.swing.UnsupportedLookAndFeelException;

import org.jfree.chart.ChartFactory;
import org.jfree.chart.ChartPanel;
import org.jfree.chart.JFreeChart;
import org.jfree.chart.axis.CategoryAxis;
import org.jfree.chart.axis.CategoryLabelPositions;
import org.jfree.chart.axis.NumberAxis;
import org.jfree.chart.plot.CategoryPlot;
import org.jfree.chart.plot.PlotOrientation;
import org.jfree.chart.renderer.category.BarRenderer;
import org.jfree.data.category.CategoryDataset;
import org.jfree.data.category.DefaultCategoryDataset;

public class Main extends NetbeansView{

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		try {
			UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
		} catch (ClassNotFoundException e) {
		} catch (InstantiationException e) {
		} catch (IllegalAccessException e) {
		} catch (UnsupportedLookAndFeelException e) {
		}
		 java.awt.EventQueue.invokeLater(new Runnable() {
	            public void run() {
	                new Main().setVisible(true);
	            }
	        });
	}

	public Main() {
		CategoryDataset dataset = createDataset();
        JFreeChart chart = createChart(dataset);
        ChartPanel chartPanel = new ChartPanel(chart, false);
        chartPanel.setPreferredSize(new Dimension(500, 270));
        panelCenter.add(chartPanel,BorderLayout.CENTER);
	}

private static CategoryDataset createDataset() {

        // row keys...
        String series1 = "First";
        String series2 = "Second";
        String series3 = "Third";

        // column keys...
        String category1 = "Category 1";
        String category2 = "Category 2";
        String category3 = "Category 3";
        String category4 = "Category 4";
        String category5 = "Category 5";

        // create the dataset...
        DefaultCategoryDataset dataset = new DefaultCategoryDataset();

        dataset.addValue(1.0, series1, category1);
        dataset.addValue(4.0, series1, category2);
        dataset.addValue(3.0, series1, category3);
        dataset.addValue(5.0, series1, category4);
        dataset.addValue(5.0, series1, category5);

        dataset.addValue(5.0, series2, category1);
        dataset.addValue(7.0, series2, category2);
        dataset.addValue(6.0, series2, category3);
        dataset.addValue(8.0, series2, category4);
        dataset.addValue(4.0, series2, category5);

        dataset.addValue(4.0, series3, category1);
        dataset.addValue(3.0, series3, category2);
        dataset.addValue(2.0, series3, category3);
        dataset.addValue(3.0, series3, category4);
        dataset.addValue(6.0, series3, category5);

        return dataset;

    }

private static JFreeChart createChart(CategoryDataset dataset) {

    // create the chart...
    JFreeChart chart = ChartFactory.createBarChart(
        "Bar Chart Demo 1",       // chart title
        "Category",               // domain axis label
        "Value",                  // range axis label
        dataset,                  // data
        PlotOrientation.VERTICAL, // orientation
        true,                     // include legend
        true,                     // tooltips?
        false                     // URLs?
    );

    // NOW DO SOME OPTIONAL CUSTOMISATION OF THE CHART...

    // set the background color for the chart...
    chart.setBackgroundPaint(Color.white);

    // get a reference to the plot for further customisation...
    CategoryPlot plot = (CategoryPlot) chart.getPlot();
    plot.setBackgroundPaint(Color.lightGray);
    plot.setDomainGridlinePaint(Color.white);
    plot.setDomainGridlinesVisible(true);
    plot.setRangeGridlinePaint(Color.white);

    // ******************************************************************
    //  More than 150 demo applications are included with the JFreeChart
    //  Developer Guide...for more information, see:
    //
    //  >   http://www.object-refinery.com/jfreechart/guide.html
    //
    // ******************************************************************

    // set the range axis to display integers only...
    final NumberAxis rangeAxis = (NumberAxis) plot.getRangeAxis();
    rangeAxis.setStandardTickUnits(NumberAxis.createIntegerTickUnits());

    // disable bar outlines...
    BarRenderer renderer = (BarRenderer) plot.getRenderer();
    renderer.setDrawBarOutline(false);

    // set up gradient paints for series...
    GradientPaint gp0 = new GradientPaint(0.0f, 0.0f, Color.blue,
            0.0f, 0.0f, new Color(0, 0, 64));
    GradientPaint gp1 = new GradientPaint(0.0f, 0.0f, Color.green,
            0.0f, 0.0f, new Color(0, 64, 0));
    GradientPaint gp2 = new GradientPaint(0.0f, 0.0f, Color.red,
            0.0f, 0.0f, new Color(64, 0, 0));
    renderer.setSeriesPaint(0, gp0);
    renderer.setSeriesPaint(1, gp1);
    renderer.setSeriesPaint(2, gp2);

    CategoryAxis domainAxis = plot.getDomainAxis();
    domainAxis.setCategoryLabelPositions(
            CategoryLabelPositions.createUpRotationLabelPositions(
                    Math.PI / 6.0));
    // OPTIONAL CUSTOMISATION COMPLETED.

    return chart;

}

}
